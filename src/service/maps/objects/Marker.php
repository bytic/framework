<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

class Nip_Service_Maps_Objects_Marker extends Nip_Service_Maps_Objects_Abstract
{
    public $latitude = false;
    public $longitude = false;

    public function __construct()
    {
        parent::__construct();
        $this->setDraggable(false);
    }

    public function setDraggable($draggable = true)
    {
        $this->setParam('draggable', (bool) $draggable);
        return $this;
    }

    public function moveOnClick($move = true)
    {
        $this->setParam('moveOnClick', (bool) $move);
        return $this;
    }

    public function addInfo($info)
    {
        $this->setParam('info', $info);
        return $this;
    }

    public function addIcon($service)
    {
    }
}
