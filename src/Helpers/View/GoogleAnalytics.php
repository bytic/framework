<?php

namespace Nip\Helpers\View;

use Nip\Config\ConfigAwareTrait;
use Nip\FlashData\FlashData;

/**
 * Class GoogleAnalytics
 * @package Nip\Helpers\View
 */
class GoogleAnalytics extends AbstractHelper
{

    use ConfigAwareTrait;

    public $transactions = null;

    protected $UA = null;

    protected $domain = null;
    
    protected $page;

    protected $operations = [];
    
    protected $flashMemory = null;

    /**
     * @param array $data
     * @see http://code.google.com/apis/analytics/docs/gaJS/gaJSApiEcommerce.html
     */
    public function addTransaction($data = [])
    {
        $order = new \stdClass();

        foreach ($data as $key => $value) {
            $order->{$key} = $value;
        }

        $this->transactions[$order->orderId] = $order;

        $this->getFlashMemory()->add("analytics.transactions", $this->transactions);
    }

    /**
     * @return FlashData
     */
    public function getFlashMemory()
    {
        if ($this->flashMemory == null) {
            $this->initFlashMemory();
        }

        return $this->flashMemory;
    }

    /**
     * @param FlashData $flashMemory
     */
    public function setFlashMemory($flashMemory)
    {
        $this->flashMemory = $flashMemory;
    }

    public function initFlashMemory()
    {
        $this->flashMemory = app('flash.data');
    }

    /**
     *
     * @param array $data
     * @see http://code.google.com/apis/analytics/docs/gaJS/gaJSApiEcommerce.html
     */
    public function addTransactionItem($data = [])
    {
        $item = new \stdClass();

        foreach ($data as $key => $value) {
            $item->{$key} = $value;
        }

        $this->transactions[$item->orderId]->items[] = $item;

        $this->getFlashMemory()->add("analytics.transactions", json_encode($this->transactions));
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->addOperation('_set', ['currencyCode', 'RON'], 'prepend');
        $this->addOperation('_trackPageview', $this->getPage() ? $this->getPage() : null, 'prepend');
        $this->addOperation('_setDomainName', $this->getDomain(), 'prepend');
        $this->addOperation('_setAccount', $this->getUA(), 'prepend');

        $this->parseTransactions('');

        $return = '<script type="text/javascript">';
        $return .= 'var _gaq = _gaq || [];';

        foreach ($this->operations as $operation) {
            $return .= "_gaq.push([";
            $return .= "'{$operation[0]}'";
            if (isset($operation[1]) && $operation[1] !== null) {
                $return .= ",";
                $params = [];
                if (is_array($operation[1])) {
                    foreach ($operation[1] as $param) {
                        $params[] = $this->renderOperationParam($param);
                    }
                } else {
                    $params[] = $this->renderOperationParam($operation[1]);
                }

                $return .= implode(',', $params);
            }
            $return .= "]);";
        }

        $return .= "(function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:'== document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();";
        $return .= '</script>';

        return $return;
    }

    /**
     * @param $method
     * @param array $params
     * @param string $position
     * @return $this
     */
    public function addOperation($method, $params = [], $position = 'below')
    {
        if ($position == 'prepend') {
            array_unshift($this->operations, [$method, $params]);
        } else {
            $this->operations[] = [$method, $params];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page ? "'$this->page'" : '';
    }

    /**
     * @param $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        if ($this->domain == null) {
            $this->initDomain();
        }

        return $this->domain;
    }

    /**
     * @param $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    protected function initDomain()
    {
        $domain = '';
        $config = $this->getConfig();
        if ($config->has('ANALYTICS.domain')) {
            $domain = $config->get('ANALYTICS.domain');
        }
        $this->setDomain($domain);
    }

    /**
     * @return null|string
     */
    public function getUA()
    {
        if ($this->UA == null) {
            $this->initUA();
        }

        return $this->UA;
    }

    /**
     * @param $code
     */
    public function setUA($code)
    {
        $this->UA = $code;
    }

    protected function initUA()
    {
        $ua = '';
        $config = $this->getConfig();
        if ($config->has('ANALYTICS.UA')) {
            $ua = $config->get('ANALYTICS.UA');
        }
        $this->setUA($ua);
    }

    /**
     * @param string $prefix
     */
    public function parseTransactions($prefix = '')
    {
        $transactions = $this->getTransactions();

        $prefix = $prefix ? $prefix . '.' : '';

        if ($transactions) {
            foreach ($transactions as $transaction) {
                $this->addOperation($prefix.'_addTrans', [
                    $transaction->orderId,
                    $transaction->affiliation,
                    $transaction->total,
                    $transaction->tax,
                    $transaction->shipping,
                    $transaction->city,
                    $transaction->state,
                    $transaction->country
                ]);

                if ($transaction->items) {
                    foreach ($transaction->items as $item) {
                        $this->addOperation($prefix.'_addItem', [
                            $item->orderId,
                            $item->sku,
                            $item->name,
                            $item->category,
                            $item->price,
                            $item->quantity
                        ]);
                    }
                }
            }
            $this->addOperation($prefix . '_trackTrans'); //submits transaction to the Analytics servers
        }
    }

    /**
     * @return null
     */
    public function getTransactions()
    {
        if ($this->transactions === null) {
            $this->initTransactions();
        }

        return $this->transactions;
    }

    public function initTransactions()
    {
        $this->transactions = json_decode($this->getFlashMemory()->get("analytics.transactions"));
    }

    /**
     * @param $param
     * @return string
     */
    public function renderOperationParam($param)
    {
        if (is_bool($param)) {
            return $param === true ? 'true' : 'false';
        }
        return "'{$param}'";
    }
}
