<?php

namespace Nip\Http\ServerMiddleware;

use Closure;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use InvalidArgumentException;
use LogicException;
use Nip\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Dispatcher
 * @package Nip\Http\ServerMiddleware
 *
 * @based on https://github.com/oscarotero/middleland/blob/master/src/Dispatcher.php
 */
class Dispatcher implements DispatcherInterface
{

    /**
     * @var MiddlewareInterface[]
     */
    private $middleware;
    /**
     * @var ContainerInterface|null
     */
    private $container;


    /**
     * @param MiddlewareInterface[] $middleware
     * @param ContainerInterface $container
     */
    public function __construct(array $middleware, ContainerInterface $container = null)
    {
        if (empty($middleware)) {
            throw new LogicException('Empty middleware queue');
        }
        $this->middleware = $middleware;
        $this->container = $container;
    }

    /**
     * Return a new dispatcher containing the given middleware.
     *
     * @param \Interop\Http\ServerMiddleware\MiddlewareInterface $middleware
     * @return DispatcherInterface
     */
    public function with(MiddlewareInterface $middleware): DispatcherInterface
    {
        return $this->withElement($middleware);
    }

    /**
     * Return a new dispatcher containing the given element.
     *
     * @param MiddlewareInterface $element
     * @return DispatcherInterface
     */
    public function withElement($element): DispatcherInterface
    {
        $this->middleware = array_merge($this->middleware, [$element]);
        return $this;
    }

    /**
     * Dispatch the request, return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        reset($this->middleware);
        return $this->get($request)->process($request, $this->createDelegate());
    }

    /**
     * Create a delegate for the current stack
     *
     * @param DelegateInterface $delegate
     *
     * @return DelegateInterface
     */
    private function createDelegate(DelegateInterface $delegate = null): DelegateInterface
    {
        return new class($this, $delegate) implements DelegateInterface {
            private $dispatcher;
            private $delegate;

            /**
             * @param Dispatcher $dispatcher
             * @param DelegateInterface|null $delegate
             */
            public function __construct(Dispatcher $dispatcher, DelegateInterface $delegate = null)
            {
                $this->dispatcher = $dispatcher;
                $this->delegate = $delegate;
            }

            /**
             * {@inheritdoc}
             */
            public function process(ServerRequestInterface $request)
            {
                $frame = $this->dispatcher->next($request);
                if ($frame === false) {
                    if ($this->delegate !== null) {
                        return $this->delegate->process($request);
                    }
                    throw new LogicException('Middleware queue exhausted');
                }
                return $frame->process($request, $this);
            }
        };
    }

    /**
     * Return the next available middleware frame in the queue.
     *
     * @param ServerRequestInterface $request
     * @return false|MiddlewareInterface
     */
    public function next(ServerRequestInterface $request)
    {
        next($this->middleware);
        return $this->get($request);
    }

    /**
     * Return the next available middleware frame in the middleware.
     *
     * @param ServerRequestInterface $request
     *
     * @return MiddlewareInterface|false
     */
    private function get(ServerRequestInterface $request)
    {
        $frame = current($this->middleware);
        if ($frame === false) {
            return $frame;
        }
        if (is_array($frame)) {
            $conditions = $frame;
            $frame = array_pop($conditions);
            foreach ($conditions as $condition) {
                if ($condition === true) {
                    continue;
                }
                if ($condition === false) {
                    return $this->next($request);
                }
                if (is_string($condition)) {
                    $condition = new Matchers\Path($condition);
                } elseif (!($condition instanceof Matchers\MatcherInterface)) {
                    throw new InvalidArgumentException(
                        'Invalid matcher. Must be a boolean, string or an instance of MatcherInterface'
                    );
                }
                if (!$condition->match($request)) {
                    return $this->next($request);
                }
            }
        }
        if (is_string($frame)) {
            if ($this->container === null) {
                throw new InvalidArgumentException(sprintf('No valid middleware provided (%s)', $frame));
            }
            $frame = $this->container->get($frame);
        }
        if ($frame instanceof Closure) {
            return $this->createMiddlewareFromClosure($frame);
        }
        if ($frame instanceof MiddlewareInterface) {
            return $frame;
        }
        throw new InvalidArgumentException(
            sprintf('No valid middleware provided (%s)', is_object($frame) ? get_class($frame) : gettype($frame))
        );
    }

    /**
     * Create a middleware from a closure
     *
     * @param Closure $handler
     *
     * @return MiddlewareInterface
     */
    private function createMiddlewareFromClosure(Closure $handler): MiddlewareInterface
    {
        return new class($handler) implements MiddlewareInterface {
            private $handler;

            /**
             * @param Closure $handler
             */
            public function __construct(Closure $handler)
            {
                $this->handler = $handler;
            }

            /**
             * {@inheritdoc}
             */
            public function process(ServerRequestInterface $request, DelegateInterface $delegate)
            {
                $response = call_user_func($this->handler, $request, $delegate);
                if (!($response instanceof ResponseInterface)) {
                    throw new LogicException('The middleware must return a ResponseInterface');
                }
                return $response;
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        reset($this->middleware);
        return $this->get($request)->process($request, $this->createDelegate($delegate));
    }
}
