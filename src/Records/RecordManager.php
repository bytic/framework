<?php

namespace Nip\Records;

use Nip\Records\Traits\HasFilters\RecordsTrait as HasFilters;
use Nip\Records\Traits\Relations\HasRelationsRecordsTrait;

/**
 * Class RecordManager
 * @package Nip\Records
 */
class RecordManager extends AbstractModels\RecordManager
{
    use HasFilters;
    use HasRelationsRecordsTrait;
}
