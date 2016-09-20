<?php

namespace Nip\Helpers\View;

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Breadcrumbs.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Breadcrumbs extends AbstractHelper
{

    protected $_items;
    protected $_viewPath = "/breadcrumbs";


    public function setViewPath($view)
    {
        $this->_viewPath = $view;
        return $this;
    }


    public function reset()
    {
        $this->_items = [];
        return $this;
    }

    public function addItem($title, $url = false, $checkUnique = true)
    {
        $data = array(
            'title' => $title,
            'url' => $url
        );

        if ($checkUnique) {
            $key = sha1(serialize($data));
        }
        if ($checkUnique) {
            if (!isset ($this->_items[$key])) {
                $this->_items[$key] = $data;
            }
        } else {
            $this->_items[] = $data;
        }
        return $this;
    }


    /**
     * Loads view
     * @return string
     */
    public function __toString()
    {
        $view = $this->getView();

        $view->breadcrumbs = $this->_items;

        return $view->load($this->_viewPath, array(), true);
    }
}