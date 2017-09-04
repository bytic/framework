<?php

namespace Nip\Records\Filters\Column;

use Nip\Database\Query\Select as SelectQuery;

/**
 * Class BasicFilter
 * @package Nip\Records\Filters\Column
 */
class WildcardFilter extends BasicFilter implements FilterInterface
{

    /**
     * @var string
     */
    protected $databaseOperation = 'LIKE%%';
}
