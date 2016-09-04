<?php

namespace Nip\Records\Filters\Column;

class BasicFilter extends AbstractFilter implements FilterInterface
{

    public function processRequest()
    {
        return false;
    }

}