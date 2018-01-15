<?php

namespace Nip\Helpers\View;

class GoogleDFP extends AbstractHelper
{
    protected $_slots = [];

    public function addSlot($unitName, $width, $height, $divId)
    {
        $this->_slots[$divId] = [
            'unitName' => $unitName,
            'width'    => $width,
            'height'   => $height,
            'divId'    => $divId,
        ];
    }

    public function getSlots()
    {
        return $this->_slots;
    }

    public function renderHead()
    {
        if (count($this->_slots)) {
            $return = $this->renderHeadScriptTag();
            $return .= $this->renderHeadScriptCommands();

            return $return;
        }
    }

    protected function renderHeadScriptTag()
    {
        $return = "<script type='text/javascript'>
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
                    </script>";

        return $return;
    }

    protected function renderHeadScriptCommands()
    {
        $return = "<script type='text/javascript'>";
        $return .= 'googletag.cmd.push(function() { ';
        $return .= $this->renderHeadSlots();
        $return .= 'googletag.pubads().enableSingleRequest(); 
                        googletag.enableServices();';
        $return .= '});';
        $return .= ' </script> ';
    }

    protected function renderHeadSlots()
    {
        $return = '';
        foreach ($this->_slots as $slot) {
            $return .= "googletag.defineSlot('".$slot['unitName']."', [".$slot['width'].', '.$slot['height']."], '".$slot['divId']."').addService(googletag.pubads());";
        }

        return $return;
    }

    public function renderAdUnit($id)
    {
        $add = $this->_slots[$id];
        if ($add) {
            return '
                <!-- '.$add['unitName']." -->
                <div id='".$add['divId']."' style='width:".$add['width'].'px; height:'.$add['height']."px;'>
                <script type='text/javascript'>
                googletag.cmd.push(function() { googletag.display('".$add['divId']."'); });
                </script>
                </div>";
        }
    }
}
