<?php

class Nip_Form_Element_Texteditor extends Nip_Form_Element_Textarea
{
    protected $_type = 'texteditor';

    protected $inputFilter;

    protected $allowedTags = [
        "a",
        "b",
        "blink",
        "blockquote",
        "br",
        "caption",
        "center",
        "col",
        "colgroup",
        "comment",
        "em",
        "font",
        "h1",
        "h2",
        "h3",
        "h4",
        "h5",
        "h6",
        "hr",
        "img",
        "li",
        "marquee",
        "ol",
        "p",
        "pre",
        "s",
        "small",
        "span",
        "strike",
        "strong",
        "sub",
        "sup",
        "table",
        "tbody",
        "td",
        "tfoot",
        "th",
        "thead",
        "tr",
        "tt",
        "u",
        "ul"
    ];

    protected $allowedAttributes = [
        "abbr",
        "align",
        "alt",
        "axis",
        "background",
        "behavior",
        "bgcolor",
        "border",
        "bordercolor",
        "bordercolordark",
        "bordercolorlight",
        "bottompadding",
        "cellpadding",
        "cellspacing",
        "char",
        "charoff",
        "cite",
        "clear",
        "color",
        "cols",
        "direction",
        "face",
        "font-weight",
        "headers",
        "height",
        "href",
        "hspace",
        "leftpadding",
        "loop",
        "noshade",
        "nowrap",
        "point-size",
        "rel",
        "rev",
        "rightpadding",
        "rowspan",
        "rules",
        "scope",
        "scrollamount",
        "scrolldelay",
        "size",
        "span",
        "src",
        "start",
        "style",
        "summary",
        "target",
        "title",
        "toppadding",
        "type",
        "valign",
        "value",
        "vspace",
        "width",
        "wrap"
    ];


    /**
     * @param $request
     * @return $this
     */
    public function getDataFromRequest($request)
    {
        $this->setValue($request);
        $this->filterHTML();
        return $this;
    }

    /**
     * @return $this
     */
    protected function filterHTML()
    {
        $this->setValue($this->getInputFilter()->purify($this->getValue()));
        return $this;
    }

    /**
     * @return HTMLPurifier
     */
    protected function getInputFilter()
    {
        if (!$this->inputFilter) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.AllowedElements', $this->allowedTags);
            $config->set('HTML.AllowedAttributes', $this->allowedAttributes);
            $purifier = new HTMLPurifier($config);
            $this->inputFilter = $purifier;
        }

        return $this->inputFilter;
    }

    public function addAllowedTags()
    {
        $items = func_get_args();
        foreach ($items as $item) {
            $this->allowedTags[] = $item;
        }
    }

    public function addAllowedAttributes()
    {
        $items = func_get_args();
        foreach ($items as $item) {
            $this->allowedAttributes[] = $item;
        }
    }
}
