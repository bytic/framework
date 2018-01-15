<?php

/**
 * Nip Framework.
 *
 * @category   Nip
 *
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */
abstract class Nip_Service_Maps_Provider_Abstract
{
    protected $_service;
    protected $_container;

    protected $_scripts = [];

    public function render()
    {
        $return = '';
        $return .= $this->initContainer();
        $return .= $this->generateScript();
        $return .= $this->loadScript();

        return $return;
    }

    public function initContainer()
    {
        $service = $this->getService();
        $this->_container = [
            'id'     => $service->getParam('container_id') ? $service->getParam('container_id') : 'map_canvas',
            'width'  => $service->getParam('container_width') ? $service->getParam('container_width') : '700',
            'height' => $service->getParam('container_height') ? $service->getParam('container_height') : '500',
        ];

        $html = '<div id="'.$this->_container['id'].'" style="width: '.$this->_container['width'].'px; height: '.$this->_container['height'].'px;">&nbsp;</div> ';

        return $html;
    }

    /**
     * @return Nip_Service_Maps
     */
    public function getService()
    {
        return $this->_service;
    }

    public function setService($service)
    {
        $this->_service = $service;

        return $this;
    }

    public function generateScript()
    {
        $return .= '<script type="text/javascript">';
        $return .= $this->initMapScript();
        $return .= $this->renderObjects();
        $return .= $this->renderScripts();
        $return .= $this->postMapScript();
        $return .= '</script>';

        return $return;
    }

    public function renderObjects()
    {
        $objects = $this->getService()->getObjects();
        $return = '';
        foreach ($objects as $object) {
            $type = $object->getType();
            $method = 'render'.inflector()->camelize($type);
            $return .= $this->$method($object);
        }

        return $return;
    }

    public function renderScripts()
    {
        $return = '';
        foreach ($this->_scripts as $script) {
            $return .= $script;
        }

        return $return;
    }

    public function renderSearch()
    {
        $return = '
            <form action="javascript:" method="post" id="map_search_form" >
                <p>
                    <input type="text" class="text tr tl br bl nomargin" value="Search map" name="location" id="location" onfocus="if(this.value==\'Search map\'){this.value=\'\'}" onblur="if(this.value==\'\'){this.value=\'Search map\'}"/>
                    <input type="submit" class="button" value="Go" />
                </p>
            </form>';

        return $return;
    }
}
