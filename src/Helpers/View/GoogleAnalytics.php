<?php

namespace Nip\Helpers\View;

use Nip_Config;

class GoogleAnalytics extends AbstractHelper
{

    public $transactions = null;
    protected $_UA = null;
    protected $_domain = null;
    protected $page;
    protected $_operations = [];
    protected $flashMemory = null;

    /**
     * @param array $data
     * @see http://code.google.com/apis/analytics/docs/gaJS/gaJSApiEcommerce.html
     */
    public function addTransaction($data = array())
    {
        $order = new \stdClass();

        foreach ($data as $key => $value) {
            $order->$key = $value;
        }

        $this->transactions[$order->orderId] = $order;

        $this->getFlashMemory()->add("analytics.transactions", $this->transactions);
    }

    /**
     * @return \Nip_Flash
     */
    public function getFlashMemory()
    {
        if ($this->flashMemory == null) {
            $this->initFlashMemory();
        }

        return $this->flashMemory;
    }

    /**
     * @param \Nip_Flash $flashMemory
     */
    public function setFlashMemory($flashMemory)
    {
        $this->flashMemory = $flashMemory;
    }

    public function initFlashMemory()
    {
        $this->flashMemory = \Nip_Flash::instance();
    }

    /**
     *
     * @param array $data
     * @see http://code.google.com/apis/analytics/docs/gaJS/gaJSApiEcommerce.html
     */
    public function addTransactionItem($data = array())
    {
        $item = new \stdClass();

        foreach ($data as $key => $value) {
            $item->$key = $value;
        }

        $this->transactions[$item->orderId]->items[] = $item;

        $this->getFlashMemory()->add("analytics.transactions", json_encode($this->transactions));
    }

    public function render()
    {

        $this->addOperation('_set', array('currencyCode', 'RON'), 'prepend');
        $this->addOperation('_trackPageview', $this->getPage() ? $this->getPage() : null, 'prepend');
        $this->addOperation('_setDomainName', $this->getDomain(), 'prepend');
        $this->addOperation('_setAccount', $this->getUA(), 'prepend');

        $this->parseTransactions('');

        $return = '<script type="text/javascript">';
        $return .= 'var _gaq = _gaq || [];';

        foreach ($this->_operations as $operation) {
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
            ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();";
        $return .= '</script>';

        return $return;
    }

    public function addOperation($method, $params = array(), $position = 'below')
    {
        if ($position == 'prepend') {
            array_unshift($this->_operations, array($method, $params));
        } else {
            $this->_operations[] = array($method, $params);
        }

        return $this;
    }

    public function getPage()
    {
        return $this->page ? "'$this->page'" : '';
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getDomain()
    {
        if ($this->_domain == null) {
            $this->setDomain(Nip_Config::instance()->ANALYTICS->domain);
        }

        return $this->_domain;
    }

    public function setDomain($domain)
    {
        $this->_domain = $domain;
    }

    public function getUA()
    {
        if ($this->_UA == null) {
            $this->setUA(Nip_Config::instance()->ANALYTICS->UA);
        }

        return $this->_UA;
    }

    public function setUA($code)
    {
        $this->_UA = $code;
    }

    public function parseTransactions($prefix = '')
    {
        $transactions = $this->getTransactions();

        $prefix = $prefix ? $prefix . '.' : '';

        if ($transactions) {
            foreach ($transactions as $transaction) {
                $this->addOperation($prefix . '_addTrans', array(
                    $transaction->orderId,
                    $transaction->affiliation,
                    $transaction->total,
                    $transaction->tax,
                    $transaction->shipping,
                    $transaction->city,
                    $transaction->state,
                    $transaction->country
                ));

                if ($transaction->items) {
                    foreach ($transaction->items as $item) {
                        $this->addOperation($prefix . '_addItem', array(
                            $item->orderId,
                            $item->sku,
                            $item->name,
                            $item->category,
                            $item->price,
                            $item->quantity
                        ));
                    }
                }
            }
            $this->addOperation($prefix . '_trackTrans'); //submits transaction to the Analytics servers
        }
    }

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

    public function renderOperationParam($param)
    {
        if (is_bool($param)) {
            return $param === true ? 'true' : 'false';
        }
        return "'{$param}'";
    }


}