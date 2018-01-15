<?php

namespace Nip\Helpers\View\Tooltips;

/**
 * Nip Framework.
 *
 * @category   Nip
 *
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @version    SVN: $Id: Item.php 23 2009-04-13 14:07:42Z victor.stanciu $
 */
class Item
{
    protected $_id;
    protected $_content;
    protected $_title;

    public function __construct($id, $content, $title = false)
    {
        $this->_id = $id;
        $this->_content = $content;
        $this->_title = $title;
    }

    public function render()
    {
        $return = '';

        $return .= '<div class="tooltip" id="'.$this->_id.'" style="display: none;">';
        if ($this->_title !== false) {
            $return .= '<div class="tooltip-heading">'.$this->_title.'</div>';
        }
        $return .= $this->_content;
        $return .= '</div>';

        return $return;
    }
}
