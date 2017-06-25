<?php
use Nip\Dispatcher\Dispatcher;

/**
 * Class Nip_Helper_Url
 */
class Nip_Helper_Url extends Nip\Helpers\AbstractHelper
{
    use \Nip\Router\RouterAwareTrait;

    protected $pieces = [];

    /**
     * Singleton
     * @return Nip_Helper_URL
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        if ($name == ucfirst($name)) {
            $this->pieces[] = Dispatcher::reverseControllerName($name);
            return $this;
        } else {
            $this->pieces[] = $name;
        }

        $name = $this->pieces ? implode(".", $this->pieces) : '';
        $this->pieces = [];
        return $this->assemble($name, isset($arguments[0]) ? $arguments[0] : null);
    }

    /**
     * @param $name
     * @param bool $params
     * @return mixed
     */
    public function assemble($name, $params = false)
    {
        return $this->getRouter()->assembleFull($name, $params);
    }

    /**
     * @param $name
     * @param bool $params
     * @return mixed
     */
    public function get($name, $params = false)
    {
        return $this->assemble($name, $params);
    }

    /**
     * @param $name
     * @param bool $params
     * @return mixed|string
     */
    public function route($name, $params = false)
    {
        return $this->getRouter()->assemble($name, $params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function base($params = [])
    {
        $currentRoute = $this->getRouter()->getCurrent();
        $base = $currentRoute ? $currentRoute->getBase($params) : request()->root();

        return $base.($params ? "?".http_build_query($params) : '');
    }

    /**
     * Reverse of the PHP built-in function parse_url
     *
     * @see http://php.net/parse_url
     * @param array $params
     * @return string
     */
    public function build($params)
    {
        return ((isset($params['scheme'])) ? $params['scheme'] . '://' : '')
        . ((isset($params['user'])) ? $params['user'] . ((isset($params['pass'])) ? ':' . $params['pass'] : '') . '@' : '')
        . ((isset($params['host'])) ? $params['host'] : '')
        . ((isset($params['port'])) ? ':' . $params['port'] : '')
        . ((isset($params['path'])) ? $params['path'] : '')
        . ((isset($params['query'])) ? '?' . $params['query'] : '')
        . ((isset($params['fragment'])) ? '#' . $params['fragment'] : '');
    }

    /**
     * Replaces all non-alphanumeric characters and returns dash-separated string
     *
     * @param string $input
     * @return string
     */
    public function encode($input)
    {
        $chars = [
            '&#x102;' => 'a',
            '&#x103;' => 'a',
            '&#xC2;' => 'A',
            '&#xE2;' => 'a',
            '&#xCE;' => 'I',
            '&#xEE;' => 'i',
            '&#x218;' => 'S',
            '&#x219;' => 's',
            '&#x15E;' => 'S',
            '&#x15F;' => 's',
            '&#x21A;' => 'T',
            '&#x21B;' => 't',
            '&#354;' => 'T',
            '&#355;' => 't'
        ];

        $change = $with = [];
        foreach ($chars as $i => $v) {
            $change[] = html_entity_decode($i, ENT_QUOTES, 'UTF-8');
            $with[] = $v;
        }
        $input = str_replace($change, $with, $input);

        preg_match_all("/[a-z0-9]+/i", $input, $sections);
        return strtolower(implode("-", $sections[0]));
    }

    public function getRequest()
    {
    }
}
