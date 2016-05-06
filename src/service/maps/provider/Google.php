<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

class Nip_Service_Maps_Provider_Google extends Nip_Service_Maps_Provider_Abstract {

    public function loadScript() {
        $lazyLoading = $this->getService()->getParam('lazyLoading');
        if ($lazyLoading) {
            $html = '
                <script type="text/javascript">
                    var script = document.createElement("script");
                    script.type = "text/javascript";
                    script.async = false;
                    script.src = "'.$this->getScriptURL().'";
                    document.documentElement.firstChild.appendChild(script);
                </script>
            ';
        } else {
            $html = '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=';
            $html .= $this->getService()->getApiKey();
            $html .= '" type="text/javascript"></script>';
            $html .= '<script type="text/javascript">loadMap();</script>';
        }
        
        return $html;
    }

    protected function getScriptURL() {
        return 'http://maps.google.com/maps?file=api&v=2&async=2&callback=loadMap&key=' . $this->getService()->getApiKey();
    }

    public function initMapScript() {
        $html = '
            function loadMap() {
            if (GBrowserIsCompatible()) {
                var map = new GMap2(document.getElementById("'.$this->_container['id'].'"));';
        $center = $this->getService()->getParam('center');
        if (is_array($center) && count($center) == 3) {
            list($cLat,$cLng, $cZoom) = $center;
        } else {
            $cLat = '37.4419';
            $cLng = '-122.1419';
            $cZoom = '1';
        }
        $html .= 'map.setCenter(new GLatLng('.$cLat.','.$cLng.' ), '.$cZoom.');';
        $html .= 'map.setUIToDefault();';
        return $html;
    }

    public function postMapScript() {
        $html = '}}';
        return $html;
    }

    public function renderSearch() {
        $return = parent::renderSearch();
        $script = '
            geocoder = new GClientGeocoder();
            form = $("map_search_form");
            form.observe("submit", function () {
                event.stop();
                address = form.location.value;
                geocoder.getLatLng(
                    address,
                    function(point) {
                        if (!point) {
                            alert(address + " not found");
                        } else {
                            map.panTo(point);
                            map.setZoom(12)
                        }
                    }
                );
            });
            ';
       $this->_scripts[] = $script;

        return $return;
    }

    public function renderMarker($marker) {
        $html = '';
        if ($marker->latitude && $marker->longitude) {
            $html .= 'var latlng = new GLatLng('.$marker->latitude.', '.$marker->longitude.');';
        } else {
            $html .= 'var latlng = map.getCenter();';
        }
        $options = array(
            'draggable' => $marker->getParam('draggable'),
        );
        $html  .= 'var marker = new GMarker(latlng, ' . json_encode($options) . ');';
        if ($marker->getParam('info')) {
            $html  .= 'GEvent.addListener(marker, "click", function() {
            marker.openInfoWindowHtml("'.$marker->getParam('info').'");
            });
            GEvent.addListener(marker, "dragstart", function() {
            map.closeInfoWindow();
            });';
        }

        if ($marker->getParam('moveOnClick')) {
            $html .= 'GEvent.addListener(map, "click", function (oldmarker, point) {
                if (oldmarker) {
                } else {
                    marker.setLatLng(point);
                    GEvent.trigger(marker, "dragend", marker);
                }
                });';
        }
       
        $listeners = $marker->getListeners();
        foreach ($listeners as $type=>$functions) {
            foreach ($functions as $function) {
                $html  .= 'GEvent.addListener(marker, "'.$type.'", function() {
                    '.$function.'
                });
                ';
            }
        }
        $html  .= 'map.addOverlay(marker);';
        return $html;
    }
}
