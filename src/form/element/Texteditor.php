<?php
class Nip_Form_Element_Texteditor extends Nip_Form_Element_Textarea {

    protected $_type = 'texteditor';

    protected $_inputFilter;
    protected $_allowedTags = array("a", "b", "blink", "blockquote", "br", "caption", "center", "col", "colgroup", "comment", "em", "font", "h1", "h2", "h3", "h4", "h5", "h6", "hr", "img", "li", "marquee", "ol", "p", "pre", "s", "small", "span", "strike", "strong", "sub", "sup", "table", "tbody", "td", "tfoot", "th", "thead", "tr", "tt", "u", "ul");
    protected $_allowedAttributes = array("abbr", "align", "alt", "axis", "background", "behavior", "bgcolor", "border", "bordercolor", "bordercolordark", "bordercolorlight", "bottompadding", "cellpadding", "cellspacing", "char", "charoff", "cite", "clear", "color", "cols", "direction", "face", "font-weight", "headers", "height", "href", "hspace", "leftpadding", "loop", "noshade", "nowrap", "point-size", "rel", "rev", "rightpadding", "rowspan", "rules", "scope", "scrollamount", "scrolldelay", "size", "span", "src", "start", "style", "summary", "target", "title", "toppadding", "type", "valign", "value", "vspace", "width", "wrap");


    public function getDataFromRequest($request) {
        $this->setValue($request);
        $this->filterHTML();
        return $this;
    }
    
    public function addAllowedTags() {
        $items = func_num_args();
        foreach ($items as $item) {
            $this->_allowedTags[] = $item;
        }
    }
   
    public function addAllowedAttributes() {
        $items = func_num_args();
        foreach ($items as $item) {
            $this->_allowedAttributes[] = $item;
        }
    }
    
    protected function filterHTML() {
       $this->setValue($this->getInputFilter()->process($this->getValue()));
       return $this;
    }


    /**
     * @return InputFilter
     */
    protected function getInputFilter() {
        if (!$this->_inputFilter) {
            $this->_inputFilter = new InputFilter($this->_allowedTags, $this->_allowedAttributes, 0, 0, 1);
        }

        return $this->_inputFilter;
    }

}