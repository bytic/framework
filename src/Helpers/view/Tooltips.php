<?php

namespace Nip\Helpers\View;

use Nip\Helpers\View\Tooltips\Item;

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Tooltips.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class Tooltips extends AbstractHelper
{
    private $tooltips = [];

    /**
     * Adds a tooltip item to the queue
     *
     * @param string $id
     * @param string $content
     * @param string|bool $title
     */
    public function addItem($id, $content, $title = false)
    {
        $this->tooltips[$id] = $this->newItem($id, $content, $title);
    }

    /**
     * New tooltip item to the queue
     *
     * @param string $id
     * @param string $content
     * @param string|bool $title
     * @return Item
     */
    public function newItem($id, $content, $title = false)
    {
        return new Item($id, $content, $title);
    }


    /**
     * Returns xHTML-formatted tooltips
     *
     * @return string
     */
    public function render()
    {
        $return = '';
        if ($this->tooltips) {
            foreach ($this->tooltips as $tooltip) {
                $return .= $tooltip->render();
            }
        }
        return $return;
    }
}
