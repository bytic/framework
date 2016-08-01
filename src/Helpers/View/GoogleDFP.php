<?php

class Nip_Helper_View_GoogleDFP extends Nip_Helper_View_Abstract
{

    protected $_slots = array();


    public function addSlot($unitName, $width, $height, $divId)
    {
        $this->_slots[$divId] = array(
            'unitName' => $unitName,
            'width' => $width,
            'height' => $height,
            'divId' => $divId,
        );
    }

    public function getSlots()
    {
        return $this->_slots;
    }

    public function renderHead()
    {
        if (count($this->_slots)) {
            $return = "
                <script type='text/javascript'>
                    var googletag = googletag || {};
                    googletag.cmd = googletag.cmd || [];
                    (function() {
                    var gads = document.createElement('script');
                    gads.async = true;
                    gads.type = 'text/javascript';
                    var useSSL = 'https:' == document.location.protocol;
                    gads.src = (useSSL ? 'https:' : 'http:') + 
                    '//www.googletagservices.com/tag/js/gpt.js';
                    var node = document.getElementsByTagName('script')[0];
                    node.parentNode.insertBefore(gads, node);
                    })();
                    </script>

                    <script type='text/javascript'>
                    googletag.cmd.push(function() {";
            foreach ($this->_slots as $slot) {
                $return .= "                    
                    googletag.defineSlot('".$slot['unitName']."', [".$slot['width'].", ".$slot['height']."], '".$slot['divId']."').addService(googletag.pubads());";
            } 
            $return .= "
                    googletag.pubads().enableSingleRequest();
                    googletag.enableServices();
                    });
                </script>";

            return $return;
        }
        return;
    }
    public function renderAdUnit($id)
    {
        $add = $this->_slots[$id]; 
        if ($add) {
            return "
                <!-- ".$add['unitName']." -->
                <div id='".$add['divId']."' style='width:".$add['width']."px; height:".$add['height']."px;'>
                <script type='text/javascript'>
                googletag.cmd.push(function() { googletag.display('".$add['divId']."'); });
                </script>
                </div>            
            ";
        }
        return;
    }

}