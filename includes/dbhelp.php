<?php
/*
 * @Author: yihua
 * @Date: 2025-01-04 17:32:11
 * @LastEditTime: 2025-01-05 16:24:41
 * @LastEditors: yihua
 * @Description: 
 * MySQL数据库操作类  
 * 1. 封装 MySQLi 扩展实现常用数据库快速操作  
 * 2. 非 ORM 实现方式，更关注 SQL 本身  
 * 3. 针对大数据表，请注意优化SQL索引及结果集规模  
 * @FilePath: \ccproxy_end\includes\dbhelp.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */


class SpringMySQLi
{
    // 公有变量  
    public $pageNo;
    public $pageRows;
    public $runCount;
    public $runTime;
    public $errNo;
    public $errMsg;

    // 私有变量  
    private $dbHost;
    private $dbUser;
    private $dbUpwd;
    private $dbName;
    private $dbChar;
    private $dbConn;

    private $querySql;
    private $queryLogs;

    // 查询历史记录计数  
    public $count;

    public function __construct($host, $user, $pwd, $dbname, $charset = 'utf8mb4')
    {
        $this->dbHost   = $host;
        $this->dbUser   = $user;
        $this->dbUpwd   = $pwd;
        $this->dbName   = $dbname;
        $this->dbChar   = $charset;

        $this->count    = 0;
        $this->querySql = '';
        $this->queryLogs = [];

        $this->pageNo   = 1;
        $this->pageRows = 10;

        $this->runCount = 0;
        $this->runTime  = 0;
        $this->errNo    = 0;
        $this->errMsg   = '';
    }

    public function __destruct()
    {
        $this->closeConnection();
    }

    public function closeConnection()
    {
        if ($this->dbConn) {
            $this->dbConn->close();
        }
    }

    public function setPageNo($num)
    {
        $this->pageNo = (int)$num;
    }

    public function setPageRows($num)
    {
        $this->pageRows = (int)$num;
    }

    public function setDbName($name)
    {
        if ($this->dbName != $name) {
            $this->dbName = $name;
            if ($this->dbConn && !$this->dbConn->select_db($name)) {
                $this->fetchError();
            }
        }
    }

    public function escape($val)
    {
        return $this->dbConn ? $this->dbConn->real_escape_string(trim($val)) : addslashes(trim($val));
    }

    public function select($sql)
    {
        $this->querySql = $sql;
        return $this->fetchResult();
    }

    public function selectPage($sql)
    {
        $this->querySql = "{$sql} LIMIT " . (($this->pageNo - 1) * $this->pageRows) . ', ' . $this->pageRows;
        return $this->fetchResult();
    }

    public function selectRow($sql)
    {
        $this->querySql = false === stripos($sql, 'LIMIT') ? "{$sql} LIMIT 1" : $sql;
        return $this->fetchResult(MYSQLI_ASSOC, true);
    }

    public function selectOne($sql)
    {
        $this->querySql = false === stripos($sql, 'LIMIT') ? "{$sql} LIMIT 1" : $sql;
        $result = $this->fetchResult(MYSQLI_NUM, true);
        return $result[0] ?? null;
    }

    public function exec($sql, array $params = [])
    {
        if ($this->connect()) {
            $stmt = $this->dbConn->prepare($sql);
            if ($stmt) {
                if ($params) {
                    // 绑定参数  
                    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
                }
                $result = $stmt->execute();
                if ($result === false) {
                    $this->fetchError();
                    return false;
                }
                return $stmt->insert_id ?: $stmt->affected_rows;
            } else {
                $this->fetchError();
                return false;
            }
        }
        return false;
    }

    public function insert($table, $values)
    {
        $vars = $this->filterVars($values);
        $this->querySql = "INSERT INTO {$table} SET {$vars}";
        return $this->exec($this->querySql);
    }

    public function update($table, $values, $where)
    {
        $val = $this->filterVars($values);
        $this->querySql = "UPDATE {$table} SET {$val} WHERE {$where}";
        return $this->exec($this->querySql);
    }

    public function delete($table, $where)
    {
        $this->querySql = "DELETE FROM {$table} WHERE {$where}";
        return $this->exec($this->querySql);
    }

    public function hasError()
    {
        return $this->errNo > 0;
    }

    public function getError()
    {
        return $this->errMsg;
    }

    public function getLogs()
    {
        return $this->queryLogs;
    }

    private function connect()
    {
        if (!$this->dbConn || !$this->dbConn->ping()) {
            $this->dbConn = new mysqli($this->dbHost, $this->dbUser, $this->dbUpwd, $this->dbName);
            if ($this->dbConn->connect_errno) {
                $this->fetchError($this->dbConn->connect_errno, $this->dbConn->connect_error);
                return false;
            }
            if (!$this->dbConn->set_charset($this->dbChar)) {
                $this->fetchError();
                return false;
            }
        }
        return true;
    }

    private function prepareStatement($sql, $params)
    {
        // 确保数据库连接已建立
        if (!$this->connect()) {
            return false;
        }
        
        $stmt = $this->dbConn->prepare($sql);
        if (!$stmt) {
            $this->fetchError(); // 捕获 prepare 失败的错误  
            return false;
        }

        if ($params) {
            // 假设所有参数都是字符串类型，对于其他类型需要适当调整  
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        return $stmt;
    }

    private function filterVars($vars)
    {
        $arr = [];
        foreach ($vars as $k => $v) {
            if (is_null($v)) {
                $arr[] = "{$k}=NULL";
            } else {
                $arr[] = "{$k}='" . $this->escape($v) . "'";
            }
        }
        return implode(',', $arr);
    }

    private function fetchResult($type = MYSQLI_ASSOC, $singleRow = false)
    {
        $result = [];
        if (stripos($this->querySql, 'SELECT') === 0) {
            if ($this->connect()) {
                $queryResult = $this->dbConn->query($this->querySql);
                if ($queryResult !== false) {
                    if ($singleRow) {
                        $result = $queryResult->fetch_array($type);
                    } else {
                        while ($row = $queryResult->fetch_array($type)) {
                            $result[] = $row;
                        }
                    }
                    $queryResult->free();
                } else {
                    $this->fetchError();
                }
            }
        } else {
            $this->fetchError(100, 'wrong query statement');
        }
        return $result;
    }

    private function fetchError($errno = null, $error = null)
    {
        $this->errNo = $errno ?? $this->dbConn->errno;
        $this->errMsg = $error ?? $this->dbConn->error;
    }

    private function fetchResultV2($statement, $type = MYSQLI_ASSOC, $singleRow = false)
    {
        $result = [];
        if ($statement && $statement->execute()) {
            $queryResult = $statement->get_result();
            if ($singleRow) {
                $result = $queryResult->fetch_array($type); // 这里传入正确的类型  
            } else {
                while ($row = $queryResult->fetch_array($type)) {
                    $result[] = $row;
                }
            }
            $queryResult->free();
        } else {
            $this->fetchError(); // 处理错误  
        }
        return $result;
    }

    public function selectRowV2($sql, array $params = [])
    {
        $sql .= " LIMIT 1";
        $stmt = $this->prepareStatement($sql, $params);
        return $this->fetchResultV2($stmt, MYSQLI_ASSOC, true);
    }

    public function selectV2($sql, array $params = [])
    {
        $stmt = $this->prepareStatement($sql, $params);
        return $this->fetchResultV2($stmt);
    }

    /**
     * 使用预处理语句插入数据
     * @param string $table 表名
     * @param array $values 键值对数组
     * @return int|false 插入的ID或影响行数，失败返回false
     */
    public function insertV2($table, array $values)
    {
        if (empty($values)) {
            return false;
        }

        if (!$this->connect()) {
            return false;
        }

        // 构建字段名和占位符
        $fields = array_keys($values);
        $placeholders = array_fill(0, count($fields), '?');
        
        // 构建 SQL 语句
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->dbConn->prepare($sql);
        if (!$stmt) {
            $this->fetchError();
            return false;
        }

        // 绑定参数
        $params = array_values($values);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $this->fetchError();
            $stmt->close();
            return false;
        }

        $result = $stmt->insert_id ?: $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    /**
     * 使用预处理语句更新数据
     * @param string $table 表名
     * @param array $values 要更新的键值对数组
     * @param string $whereClause WHERE子句（使用?占位符）
     * @param array $whereParams WHERE子句的参数
     * @return int|false 影响的行数，失败返回false
     */
    public function updateV2($table, array $values, $whereClause, array $whereParams = [])
    {
        if (empty($values)) {
            return false;
        }

        if (!$this->connect()) {
            return false;
        }

        // 构建 SET 子句
        $setClause = [];
        foreach (array_keys($values) as $field) {
            $setClause[] = "{$field} = ?";
        }

        // 构建 SQL 语句
        $sql = "UPDATE {$table} SET " . implode(', ', $setClause) . " WHERE {$whereClause}";

        $stmt = $this->dbConn->prepare($sql);
        if (!$stmt) {
            $this->fetchError();
            return false;
        }

        // 合并 SET 参数和 WHERE 参数
        $allParams = array_merge(array_values($values), $whereParams);
        $types = str_repeat('s', count($allParams));
        $stmt->bind_param($types, ...$allParams);

        if (!$stmt->execute()) {
            $this->fetchError();
            $stmt->close();
            return false;
        }

        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    /**
     * 使用预处理语句删除数据
     * @param string $table 表名
     * @param string $whereClause WHERE子句（使用?占位符）
     * @param array $whereParams WHERE子句的参数
     * @return int|false 影响的行数，失败返回false
     */
    public function deleteV2($table, $whereClause, array $whereParams = [])
    {
        if (!$this->connect()) {
            return false;
        }

        // 构建 SQL 语句
        $sql = "DELETE FROM {$table} WHERE {$whereClause}";

        $stmt = $this->dbConn->prepare($sql);
        if (!$stmt) {
            $this->fetchError();
            return false;
        }

        // 绑定参数
        if (!empty($whereParams)) {
            $types = str_repeat('s', count($whereParams));
            $stmt->bind_param($types, ...$whereParams);
        }

        if (!$stmt->execute()) {
            $this->fetchError();
            $stmt->close();
            return false;
        }

        $result = $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    /**
     * 使用预处理语句分页查询
     * @param string $sql SQL语句（使用?占位符）
     * @param array $params 参数数组
     * @return array 查询结果
     */
    public function selectPageV2($sql, array $params = [])
    {
        if (!$this->connect()) {
            return [];
        }

        // 计算 LIMIT 偏移量
        $offset = ($this->pageNo - 1) * $this->pageRows;
        
        // 添加 LIMIT 子句
        $sql .= " LIMIT {$offset}, {$this->pageRows}";

        $stmt = $this->prepareStatement($sql, $params);
        return $this->fetchResultV2($stmt);
    }
}
