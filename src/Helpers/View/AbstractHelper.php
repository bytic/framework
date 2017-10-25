<?php

namespace Nip\Helpers\View;

use Nip\Helpers\AbstractHelper as NipAbstractHelper;
use Nip\View;

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Abstract.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */
abstract class AbstractHelper extends NipAbstractHelper
{
    protected $_view;

    public function setView(View $view)
    {
        $this->_view = $view;
    }

    public function getView()
    {
        if (!$this->_view) {
            $this->initView();
        }
        return $this->_view;
    }

    public function initView()
    {
        $this->_view = View::instance();
    }
}
