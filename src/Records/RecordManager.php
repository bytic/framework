<?php

namespace Nip\Records;

use Nip\Records\Traits\HasFilters\RecordsTrait as HasFilters;

/**
 * Class RecordManager
 * @package Nip\Records
 */
class RecordManager extends AbstractModels\RecordManager
{
    use HasFilters;
}
