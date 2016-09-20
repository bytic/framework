<?php
namespace Nip\Helpers\View;

use Nip\Helpers\View\CachebleBlocks\Block;

class CachebleBlocks extends AbstractHelper
{

    private $_blocks = [];


    public function add($name)
    {
        $block = $this->newBlock($name);
        $this->_blocks[$name] = $block;
        return $block;
    }

    public function newBlock($name)
    {
        $block = new Block();
        $block->setManager($this);
        $block->setName($name);
        return $block;
    }

    public function get($name)
    {
        return $this->_blocks[$name];
    }
}