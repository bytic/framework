<?php

namespace Nip\DebugBar\DataCollector;

use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Nip\Database\Adapters\Profiler\QueryProfile;

/**
 * Collects data about SQL statements executed with PDO
 */
class QueryCollector extends PDOCollector
{
    protected $timeCollector;
    protected $queries = [];
    protected $renderSqlWithParams = false;
    protected $findSource = false;
    protected $explainQuery = false;
    protected $explainTypes = ['SELECT']; // ['SELECT', 'INSERT', 'UPDATE', 'DELETE']; for MySQL 5.6.3+
    protected $showHints = false;
    protected $reflection = [];

    /**
     * @param TimeDataCollector $timeCollector
     */
    public function __construct(TimeDataCollector $timeCollector = null)
    {
        $this->timeCollector = $timeCollector;
    }

    /**
     * Enable/disable finding the source
     *
     * @param bool $value
     */
    public function setFindSource($value = true)
    {
        $this->findSource = (bool) $value;
    }

    /**
     *
     * @param QueryProfile $profile
     */
    public function addQuery($profile)
    {
        $explainResults = [];
        $query = $profile->getQuery();

        // Run EXPLAIN on this query (if needed)
        if ($this->explainQuery && preg_match('/^(' . implode($this->explainTypes) . ') /i', $query)) {
            $result = $profile->getAdapter()->query('EXPLAIN ' . $query);
            $explainResults = $profile->getAdapter()->fetchArray($result);
        }

        $source = null;
        if ($this->findSource) {
            try {
                $source = $this->findSource();
            } catch (\Exception $e) {
            }
        }

        $this->queries[] = [
            'query' => $query,
            'time' => $profile->getTime(),
            'row_count' => $profile->affectedRows,
            'memory_str' => $profile->getMemory(),
            'source' => $source,
            'explain' => $explainResults,
//            'connection' => $profile->getAdapter()->getDatabaseName()
        ];

        if ($this->timeCollector !== null) {
            $this->timeCollector->addMeasure($query, $profile->getStartMicrotime(), $profile->getEndMicrotime());
        }
    }

    /**
     * Use a backtrace to search for the origin of the query.
     */
    protected function findSource()
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT);
        foreach ($traces as $trace) {
            if (isset($trace['class']) && isset($trace['file']) && strpos(
                    $trace['file'],
                    DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR
                ) === false
            ) {
                $file = $trace['file'];
                $line = isset($trace['line']) ? $trace['line'] : '?';

                return $this->normalizeFilename($file) . ':' . $line;
            } elseif (isset($trace['function']) && $trace['function'] == 'Illuminate\Routing\{closure}') {
                return 'Route binding';
            }
        }
    }


    /**
     * Shorten the path by removing the relative links and base dir
     *
     * @param string $path
     * @return string
     */
    protected function normalizeFilename($path)
    {
        if (file_exists($path)) {
            $path = realpath($path);
        }
        return str_replace(base_path(), '', $path);
    }

    /**
     * Reset the queries.
     */
    public function reset()
    {
        $this->queries = [];
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $totalTime = 0;
        $queries = $this->queries;

        $statements = [];
        foreach ($queries as $query) {
            $totalTime += $query['time'];

            $statements[] = [
                'sql' => $this->formatSql($query['query']),
                'row_count' => $query['row_count'],
                'duration' => $query['time'],
                'memory_str' => $query['memory_str'],
                'duration_str' => $this->formatDuration($query['time']),
                'stmt_id' => $query['source'],
//                'connection' => $query['connection'],
            ];

            //Add the results from the explain as new rows
            foreach ($query['explain'] as $explain) {
                $statements[] = [
                    'sql' => ' - EXPLAIN #' . $explain->id . ': `' . $explain->table . '` (' . $explain->select_type . ')',
                    'params' => $explain,
                    'row_count' => $explain->rows,
                    'stmt_id' => $explain->id,
                ];
            }
        }

        $data = [
            'nb_statements' => count($queries),
            'nb_failed_statements' => 0,
            'accumulated_duration' => $totalTime,
            'accumulated_duration_str' => $this->formatDuration($totalTime),
            'statements' => $statements
        ];
        return $data;
    }

    /**
     * Removes extra spaces at the beginning and end of the SQL query and its lines.
     *
     * @param string $sql
     * @return string
     */
    protected function formatSql($sql)
    {
        return trim(preg_replace("/\s*\n\s*/", "\n", $sql));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'queries';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "queries" => [
                "icon" => "inbox",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "queries",
                "default" => "[]"
            ],
            "queries:badge" => [
                "map" => "queries.nb_statements",
                "default" => 0
            ]
        ];
    }
}
