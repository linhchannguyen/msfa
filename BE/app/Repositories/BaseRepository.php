<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\AuthTrait;
use App\Services\SystemParameterService;
use App\Repositories\SystemParameter\SystemParameterRepository;
use App\Enums\LogBatchJob;

class BaseRepository implements BaseRepositoryInterface
{

    use AuthTrait;

    /**
     * Turn on/off automatic updating meta column
     * (only for _create and _update)
     * @var $useAutoMeta = true
     */
    protected $useAutoMeta = false;

    /**
     * @param string $sql
     * @return stdClass
     */
    public function _all($sql)
    {
        return $this->_tryExecuteReturn(function () use ($sql) {
            return DB::select($sql);
        });
    }

    /**
     * @param string $sql
     * @param array $data
     * @return stdClass
     */
    public function _find($sql, $data)
    {
        return $this->_tryExecuteReturn(function () use ($sql, $data) {
            return DB::select($sql, $data);
        });
    }

    /**
     * @param string $sql
     * @return stdClass
     */
    public function _first($sql, $data)
    {
        $records = $this->_find($sql, $data);
        return $records[0] ?? [];
    }

    /**
     * @param string $table
     * @param string $orderedColumn
     * @return stdClass
     */
    public function _lastInserted($table, $orderedColumn)
    {
        $sql = "SELECT * FROM $table ORDER BY $orderedColumn DESC LIMIT 1";
        return $this->_first($sql, []);
    }

    /**
     * @param string $sql
     * @param array $data = []
     * @return boolean
     */
    public function _create($sql, $data = [], $type = LogBatchJob::TYPE_API, $contentLog = '', $keyBatchJob = null)
    {
        if ($this->useAutoMeta) {
            $result = $this->_createWithMetaFields($sql, $data);
            if ( $type == LogBatchJob::TYPE_BATCH_JOB ) {
                logCountRowActionSql($contentLog, LogBatchJob::ACTION_INSERT, $keyBatchJob);
            }
            return $result;
        }

        return $this->_tryExecute(function () use ($sql, $data, $type, $contentLog, $keyBatchJob) {
            if (empty($data)) {
                $result = DB::insert($sql);
            } else {
                $result = DB::insert($sql, $data);
            }
            if ( $type == LogBatchJob::TYPE_BATCH_JOB ) {
                logCountRowActionSql($contentLog, LogBatchJob::ACTION_INSERT, $keyBatchJob);
            }
            return $result;
        });
    }

    private function __bulkCreateWithoutMetaFields($fields, $sql, $data)
    {
        // Building inserting data
        $insertValues = '';
        foreach ($data as $row) {
            $insertValues .= '(';
            $rowValues = '';
            foreach ($fields as $field) {
                $value = $row[$field];

                if (is_string($value)) {
                    $rowValues .= "'$value',";
                } else {
                    $rowValues .= "$value,";
                }
            }
            $rowValues = trim($rowValues, ',');
            $insertValues .= $rowValues;
            $insertValues .= '),';
        }

        // Trim last comma
        $insertValues = trim($insertValues, ',');

        // Replace insert values into statement
        return str_replace(':values', $insertValues, $sql);
    }

    private function __bulkCreateWithMetaFields($fields, $sql, $data)
    {
        // Inject meta columns into sql query
        $metaColumns = $this->__getMetaColumns('create');
        $regex = "/INSERT INTO ([\w\W]+)\(([\w\W]+)\)\s*VALUES([\w\W]+)/";
        preg_match_all($regex, $sql, $matches);
        $metaFields = PHP_EOL . implode(',' . PHP_EOL, array_keys($metaColumns)) . ',';
        $newSql = "INSERT INTO {$matches[1][0]}($metaFields{$matches[2][0]}) VALUES :values;";

        // Inject meta values
        $fields = array_merge(array_keys($metaColumns), $fields);

        // Building inserting data
        $insertValues = '';
        foreach ($data as $row) {
            $insertValues .= '(';
            $rowValues = '';

            // Inject meta values
            $row = array_merge($metaColumns, $row);

            foreach ($fields as $field) {
                $value = trim($row[$field], "'");

                if (is_string($value)) {
                    $rowValues .= "'$value',";
                } else {
                    $rowValues .= "$value,";
                }
            }
            $rowValues = trim($rowValues, ',');
            $insertValues .= $rowValues;
            $insertValues .= '),';
        }

        // Trim last comma
        $insertValues = trim($insertValues, ',');

        // Replace insert values into statement
        return str_replace(':values', $insertValues, $newSql);
    }

    /**
     * @param string $sql
     * @param array $data
     * @return boolean
     *
     * Example: _bulkCreate("INSERT INTO table(col1, col2) VALUES :values", [["col1" => "The value 1", "col2" => "The value 2"], ["col1" => "The value 3", "col2" => "The value 4"]]);
     *
     */
    public function _bulkCreate($sql, $data)
    {

        if (empty($data)) {
            return false;
        }

        // Get order of insert fields
        preg_match_all("/\(([\w\W]+)\)/", $sql, $fields);

        if (empty($fields)) {
            return false;
        }

        $fields = preg_replace("/([\s]*)/", "", $fields[1][0]);
        $fields = explode(',', $fields);

        if ($this->useAutoMeta) {
            $bulkSql = $this->__bulkCreateWithMetaFields($fields, $sql, $data);
        } else {
            $bulkSql = $this->__bulkCreateWithoutMetaFields($fields, $sql, $data);
        }

        return $this->_tryExecute(function () use ($bulkSql) {
            return DB::insert($bulkSql);
        });
    }

    /**
     * @param string $sql
     * @param array $data = []
     * @return boolean
     */
    public function _update($sql, $data, $type = LogBatchJob::TYPE_API, $contentLog = '', $keyBatchJob = null)
    {
        $sql1 = "SET SQL_SAFE_UPDATES = 0;";
        DB::select($sql1);
        if ($this->useAutoMeta) {
            $result = $this->_updateWithMetaFields($sql, $data);
            if ( $type == LogBatchJob::TYPE_BATCH_JOB ) {
                logCountRowActionSql($contentLog, LogBatchJob::ACTION_UPDATE, $keyBatchJob);
            }
            return $result;
        }

        return $this->_tryExecute(function () use ($sql, $data, $type, $contentLog, $keyBatchJob) {
            $this->filterDataUpdate($sql, $data);
            $result = DB::update($sql, $data);
            if ( $type == LogBatchJob::TYPE_BATCH_JOB ) {
                logCountRowActionSql($contentLog, LogBatchJob::ACTION_UPDATE, $keyBatchJob);
            }
            return $result;
        });
    }

    /**
     * @param string $sql
     * @param array $data = []
     * @return boolean
     */
    public function _destroy($sql, $data, $type = LogBatchJob::TYPE_API, $contentLog = '', $keyBatchJob = null)
    {
        return $this->_tryExecute(function () use ($sql, $data, $type, $contentLog, $keyBatchJob) {
            $sql1 = "SET SQL_SAFE_UPDATES = 0;";
            $sql2 = "SET FOREIGN_KEY_CHECKS = 0;";
            DB::select($sql1);
            DB::select($sql2);

            DB::delete($sql, $data);
            if ( $type == LogBatchJob::TYPE_BATCH_JOB ) {
                logCountRowActionSql($contentLog, LogBatchJob::ACTION_DELETE, $keyBatchJob);
            }

            $sql3 = "SET SQL_SAFE_UPDATES = 1;";
            $sql4 = "SET FOREIGN_KEY_CHECKS = 1;";
            DB::select($sql3);
            DB::select($sql4);

            return true;
        });
    }

    /**
     * @param string $sql
     * @param array $data
     * @param $paginateParams [
     *      'limit' => 10,
     *      'offset' => 0,
     *      'key' => '* OR the column alias name',
     *      'key_type' => 'json OR normal'
     * ]
     */
    public function _paginate($sql, $data, $paginateParams = [
        'limit' => 10,
        'offset' => 0,
        'key' => '*',
        'key_type' => 'normal'
    ])
    {
        $limit = (int)$paginateParams['limit'];
        $offset_current = (int)$paginateParams['offset'];

        $sql = str_replace(';','', $sql);
        $sql_count = 'SELECT count(1) as cnt FROM ('. $sql . ') as x';
        $result = DB::select( DB::raw($sql_count), $data );
        $dataSqlCount = $result[0]->cnt;

        $offset = $offset_current * $limit;

        $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        $records = DB::select( DB::raw($sql), $data );

        if (count($records) == 0) {
            return [
                "records" => [],
                "pagination" => [
                    "total_pages" => 0,
                    "previous_page" => 1,
                    "next_page" => 1,
                    "current_page" => 1,
                    "first_page" => 1,
                    "last_page" => 1
                ]
            ];
        }

        // Calculate pagination data
        $totalPage = ceil($dataSqlCount / $limit);
        $currentPage = $offset_current + 1;
        $previousPage = ($currentPage - 1) > 1 ? $currentPage - 1 : 1;
        $nextPage = ($currentPage + 1) < $totalPage ? $currentPage + 1 : $totalPage;
        $firstPage = 1;
        $lastPage = $totalPage;

        return [
            "records" => $records,
            "pagination" => [
                "total_items" => $dataSqlCount,
                "total_pages" => $totalPage,
                "previous_page" => $previousPage,
                "next_page" => $nextPage,
                "current_page" => $currentPage,
                "first_page" => $firstPage,
                "last_page" => $lastPage
            ]
        ];
    }

    /**
     * @param array $data
     * @param boolean $withBrackets = false
     * @return string
     */
    public function _buildCommaString($data, $withBrackets = false)
    {
        $string = '';

        foreach ($data as $param) {
            if (is_string($param)) {
                $string .= "'$param',";
            } else {
                $string .= "$param,";
            }
        }

        $string = trim($string, ',');

        if ($withBrackets) {
            $string = "($string)";
        }

        return $string;
    }

    private function __getMetaColumns(string $type)
    {
        $metaColumns = [];
        $systemParameterService = new SystemParameterService(new SystemParameterRepository());
        $currentDateTime = $systemParameterService->getCurrentSystemDateTime();
        $currentDateTimeUpdate = date('Y-m-d H:i:s', strtotime('+1 mins', strtotime($currentDateTime)));
        $proxyUserCd = $this->getCurrentProxyUser();
        $userCd = $this->getCurrentUser() ?? 'system';

        // If is proxy user
        if ($this->isProxyUser()) {
            if ($type == 'create') {
                $metaColumns = [
                    'created_by' => "$userCd",
                    'proxy_created_by' => "$proxyUserCd",
                    'created_at' => "$currentDateTime",
                    'updated_by' => "$userCd",
                    'proxy_updated_by' => "$proxyUserCd",
                    'updated_at' => "$currentDateTime"
                ];
            } elseif ($type == 'update') {
                $metaColumns = [
                    "updated_by" => $userCd,
                    "proxy_updated_by" => $proxyUserCd,
                    "updated_at" => $currentDateTimeUpdate
                ];
            }
        } else {
            if ($type == 'create') {
                $metaColumns = [
                    'created_by' => $userCd,
                    'created_at' => $currentDateTime,
                    'updated_by' => $userCd,
                    'updated_at' => $currentDateTime,
                    "proxy_updated_by" => NULL
                ];
            } elseif ($type == 'update') {
                $metaColumns = [
                    "updated_by" => $userCd,
                    "updated_at" => $currentDateTimeUpdate,
                    "proxy_updated_by" => NULL
                ];
            }
        }

        return $metaColumns;
    }

    private function _createWithMetaFields($sql, $data)
    {
        $metaColumns = $this->__getMetaColumns('create');
        $sql = trim(preg_replace('/\s\s+/', ' ', $sql));
        if ($this->isProxyUser()) {
            if (strpos($sql, ") VALUES")) {
                $sql = str_replace(") VALUE", ",created_by,proxy_created_by,created_at,updated_by,proxy_updated_by,updated_at) VALUE", $sql);
            }
            if (strpos($sql, ") ON")) {
                $sql = str_replace(") ON", ",:created_by,:proxy_created_by,:created_at,:updated_by,:proxy_updated_by,:updated_at) ON", $sql);
            } else {
                $sql = str_replace(");", ",:created_by,:proxy_created_by,:created_at,:updated_by,:proxy_updated_by,:updated_at);", $sql);
            }
        } else {
            if (strpos($sql, ") VALUES")) {
                $sql = str_replace(") VALUE", ",created_by,created_at,updated_by,updated_at) VALUE", $sql);
            }
            if (strpos($sql, ") ON")) {
                $sql = str_replace(") ON", ",:created_by,:created_at,:updated_by,:updated_at) ON", $sql);
            } else {
                $sql = str_replace(");", ",:created_by,:created_at,:updated_by,:updated_at);", $sql);
            }
        }
        $data = array_merge($data, $metaColumns);
        return $this->_tryExecute(function () use ($sql, $data) {
            if (empty($data)) {
                return DB::insert($sql);
            } else {
                return DB::insert($sql, $data);
            }
        });
    }

    private function _updateWithMetaFields($sql, $data)
    {
        $metaColumns = $this->__getMetaColumns('update');
        $sql = trim(preg_replace('/\s\s+/', ' ', $sql));
        if ($this->isProxyUser()) {
            if (strpos($sql, "SET ")) {
                $sql = str_replace("SET ", "SET updated_by = :updated_by,proxy_updated_by = :proxy_updated_by,updated_at = :updated_at,", $sql);
            }
        } else {
            if (strpos($sql, "SET ")) {
                $sql = str_replace("SET ", "SET updated_by = :updated_by,updated_at = :updated_at,", $sql);
            }
        }
        $data = array_merge($data, $metaColumns);
        // Execute query
        return $this->_tryExecute(function () use ($sql, $data) {
            return DB::update($sql, $data);
        });
    }

    private function _tryExecute(callable $callback, $returnData = false)
    {
        $status = true;
        $exception = '';
        $data = [];
        try {
            $data = $callback();
            $status = $data !== false;
        } catch (ModelNotFoundException $ex) {
            $exception = $ex;
            $status = false;
        } catch (\Exception $ex) {
            // dd($ex->getMessage());
            throw $ex;
            $exception = $ex;
            $status = false;
        }

        if (!$status && env('APP_DEBUG')) {
            throw $exception;
        }

        return $returnData ? $data : $status;
    }

    private function _tryExecuteReturn(callable $callback)
    {
        return $this->_tryExecute($callback, true);
    }

    /**
     * Pluck items by key
     * @param array $data
     * @param string $key
     */
    public function _pluck($data, string $key)
    {
        return Arr::pluck($data, $key);
    }

    function filterDataUpdate(&$sql, &$data)
    {
        $updateTable = substr($sql, 0, strpos($sql, 'SET'));
        $whereTable = substr($sql . ";", strpos($sql . ";", 'WHERE'), -1);
        $systemParameterService = new SystemParameterService(new SystemParameterRepository());
        $currentDateTime = $systemParameterService->getCurrentSystemDateTime();
        $currentDateTimeUpdate = date('Y-m-d H:i:s', strtotime('+1 mins', strtotime($currentDateTime)));
        $proxyUserCd = $this->getCurrentProxyUser();
        $userCd = $this->getCurrentUser() ?? 'system';
        preg_match_all('/(?<set>set)(?<sql>.+)(?<where>where)(?<endsql>.*$)/msi', $sql, $matches);
        // get param 
        $sqlSet = trim($matches['sql'][0]);
        $paramSet = explode(',', $sqlSet);
        // get params not change
        $paramUsage = []; // params used to sql 
        foreach ($paramSet as $param) {
            preg_match_all('/(?<operator>:)(?<params>.[^\s|^)|^,|^;]*)/msi', $param, $matcheParams);
            if (@$matcheParams['params']) {
                $paramUsage = array_merge($paramUsage, $matcheParams['params']);
            }
        }
        /**
         * Add new auto field into sql
         */
        if ($this->isProxyUser()) {
            $data['updated_by'] = $userCd;
            $data['proxy_updated_by'] = $proxyUserCd;
            $data['updated_at'] = $currentDateTimeUpdate;
            $paramUsage = array_merge(
                $paramUsage,
                [
                    'updated_by',
                    'proxy_updated_by',
                    'updated_at'
                ]
            );
        } else {
            $data['updated_by'] = $userCd;
            $data['updated_at'] = $currentDateTimeUpdate;
            $data['proxy_updated_by'] = NULL;
            $paramUsage = array_merge(
                $paramUsage,
                [
                    'updated_by',
                    'updated_at',
                    'proxy_updated_by'
                ]
            );
        }
        $paramUsage = array_unique($paramUsage);
        $paramSet = "";
        if (!empty($paramUsage)) {
            foreach ($paramUsage as $param) {
                $paramSet .= "$param = :$param,";
            }
        }
        $paramSet = rtrim($paramSet, ',');
        $sql = $updateTable . " SET " . $paramSet . " " . $whereTable;
    }
}
