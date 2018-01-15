<?php

namespace Nip\Helpers\View;

class Sorter extends AbstractHelper
{
    protected $_sorter;
    protected $_url;

    public function render($field, $label)
    {
        $url = $this->getURL($field, 'asc');
        if ($field == $this->getSorter()->getField()) {
            switch ($this->getSorter()->getType()) {
                case 'desc':
                    $url = $this->getURL($field, 'asc');
                    $image = 'arrow_up';
                    break;
                case 'asc':
                default:
                    $url = $this->getURL($field, 'desc');
                    $image = 'arrow_down';
                    break;
            }
        }
        $return = "<a href=\"$url\">";
        if ($image) {
            $return .= '<img src="'.Nip_Helper_URL::instance()->image("$image.gif").'" alt="">';
        }
        $return .= $label;
        $return .= '</a>';

        return $return;
    }

    /**
     * @param Nip_Record_Sorter $sorter
     *
     * @return $this
     */
    public function setSorter($sorter)
    {
        $this->_sorter = $sorter;

        return $this;
    }

    /**
     * @return $this
     */
    public function getSorter()
    {
        return $this->_sorter;
    }

    public function setURL($url)
    {
        $this->_url = html_entity_decode($url);

        return $this;
    }

    public function getURL($field = false, $type = false)
    {
        $url = $this->_url;
        $url = parse_url($url);

        $query = $url['query'];
        parse_str($query, $params);

        $params['order'] = $field;
        if ($type) {
            $params['order_type'] = $type;
        }

        $url['query'] = http_build_query($params);

        return htmlentities(Nip_Helper_URL::instance()->build($url));
    }
}
