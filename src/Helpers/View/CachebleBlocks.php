<?php

class Nip_Helper_View_CachebleBlocks extends Nip_Helper_View_Abstract
{

	private $_blocks = array();   
    
    
    public function add($name)
    {
        $block = $this->newBlock($name);
        $this->_blocks[$name] = $block;
        return $block;
    }
    
    public function get($name)
    {
        return $this->_blocks[$name];
    }  
    
    public function newBlock($name)
    {
        $block = new Nip_Helper_View_CachebleBlocks_Block();
        $block->setManager($this);
        $block->setName($name);
        return $block;
    }     
    
    /**
     * Singleton
     *
     * @return Nip_Helper_View_CachebleBlocks
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}