<?php

namespace Nip\DebugBar\Formatter;

use Monolog\Formatter\HtmlFormatter;

/**
 * Class MonologFormatter
 * @package Nip\DebugBar\Formatter
 */
class MonologFormatter extends HtmlFormatter
{
    public function format(array $record)
    {
        $title = $record['level_name'] . ' ' . (string) $record['message'];
        $return = str_pad($title, 100, " ");
        $return .= parent::format($record);
        return $return;
    }
}
