<?php
/**
 * MySQL数据库操作类
 * 1. 封装 MySQLi 扩展实现常用数据库快速操作
 * 2. 非 ORM 实现方式，更关注 SQL 本身
 * 3. 针对大数据表，请注意优化SQL索引及结果集规模
 *
 * @version:  1.1 <2021-05-25 15:00>
 * @author:   james zhang <james@springphp.com>
 * @license:  Apache Licence 2.0
 */

class SpringMySQLi
{
    //public variables
    public $pageNo;         // current page number
    public $pageRows;       // record number per page

    public $runCount;       // query count
    public $runTime;        // consumed time

    public $errNo;          // error code
    public $errMsg;         // error msg

    //private variables
    private $dbHost;        // database host
    private $dbUser;        // database username
    private $dbUpwd;        // database password
    private $dbName;        // database name
    private $dbChar;        // connection charset
    private $dbConn;        // connection handler

    private $querySql;      // query statement
    private $queryLogs;     // query history

    public $count;     // query history
    /**
     * class constructor
     */
    function __construct($host, $user, $pwd, $dbname, $charset = 'utf8mb4')
    {
        $this->dbHost   = $host;
        $this->dbUser   = $user;
        $this->dbUpwd   = $pwd;
        $this->dbName   = $dbname;
        $this->dbChar   = $charset;
        $this->dbConn   = false;


        $this->count    =0;
        $this->querySql = '';
        $this->queryLogs= array();

        $this->pageNo   = 1;
        $this->pageRows = 10;

        $this->runCount = 0;
        $this->runTime  = 0;
        $this->errNo    = 0;
        $this->errMsg   = '';
    }

    /**
     * destroy class
     */
    function __destruct()
    {
        if ($this->dbConn) {
            $this->dbConn->close();
        }
    }

    /**
     * destroy class
     */
    public function destory()
    {
        if ($this->dbConn) {
            $this->dbConn->close();
        }
    }

    /**
     * set page number
     */
    public function setPageNo($num)
    {
        $this->pageNo = $num;
    }

    /**
     * set rows per page
     */
    public function setPageRows($num)
    {
        $this->pageRows = $num;
    }

    /**
     * set default database
     */
    public function setDbName($name)
    {
        if ($this->dbName != $name) {
            $this->dbName = $name;
            if ($this->dbConn) {
                if (!$this->dbConn->select_db($name)) {
                    $this->fetchError();
                }
            }
        }
    }

    /**
     * filter query parameter
     */
    public function escape($val)
    {
        if ($this->dbConn)
            return $this->dbConn->real_escape_string(stripslashes(trim($val)));
        else
            return addslashes(stripslashes(trim($val)));
    }

    /**
     * return array result from a query
     */
    public function select($sql)
    {
        if (2 <= func_num_args()) {
            $this->querySql = $this->fetchArgs(func_get_args());
        } else {
            $this->querySql = $sql;
        }

        return $this->fetchResult();
    }
    
    /**
     * return array result from a query and page config
     */
    public function selectPage($sql)
    {
        if (2 <= func_num_args()) {//该功能可以配合使用func_get_arg()和func_get_args()允许用户自定义函数接收可变长度参数列表。
            $sql = $this->fetchArgs(func_get_args());
        }
        $this->querySql = "{$sql} LIMIT " . (($this->pageNo - 1) * $this->pageRows) . ', ' . $this->pageRows;

        return $this->fetchResult();
    }

    /**
     * select the first row of query result
     */
    public function selectRow($sql)
    {
        if (2 <= func_num_args()) {
            $sql = $this->fetchArgs(func_get_args());
        }
        if (false == stripos($sql, 'LIMIT')) {
            $this->querySql = "{$sql} LIMIT 1";
        } else {
            $this->querySql = $sql;
        }
        return $this->fetchResult(MYSQLI_ASSOC, true);
    }

    /**
     * select the first column of the first row
     */
    public function selectOne($sql)
    {
        if (2 <= func_num_args()) {
            $sql = $this->fetchArgs(func_get_args());
        }
        if (false == stripos($sql, 'LIMIT')) {
            $this->querySql = "{$sql} LIMIT 1";
        } else {
            $this->querySql = $sql;
        }

        $result = $this->fetchResult(MYSQLI_NUM, true);
        return (isset($result[0]) ? $result[0] : null);
    }

    /**
     * select two column into a hash array
     */
    public function selectHash($sql)
    {
        if (2 <= func_num_args()) {
            $this->querySql = $this->fetchArgs(func_get_args());
        } else {
            $this->querySql = $sql;
        }

        $arr = $this->fetchResult(MYSQLI_NUM);
        $map = array();
        foreach ($arr as $row) {
            $map[$row[0]] = $row[1];
        }
        unset($arr);
        return $map;
    }
/**
 * Undocumented function
 *
 * @param [type] $_sql
 * @param [type] $_array
 * @author 一花 <487735913@qq.com>
 * @copyright run a sql function [type]  [type]
 */
    public function exec($_sql, $_array = null)
	{
		if (is_array($_array)) {
			$stmt = $this->db->prepare($_sql);
			if($stmt) {
				$result = $stmt->execute($_array);
				if($result!==false){
					return $result;
				}else{
					$this->errorInfo = $stmt->errorInfo();
					return false;
				}
			}else{
				$this->errorInfo = $this->db->errorInfo();
				return false;
			}
		} else {
			$result = $this->db->exec($_sql);
			if($result!==false){
				return $result;
			}else{
				$this->errorInfo = $this->db->errorInfo();
				return false;
			}
		}
	}
    /**
     * run a sql
     */
    public function exe($sql)
    {
        if (2 <= func_num_args()) {
            $this->querySql = $this->fetchArgs(func_get_args());
        } else {
            $this->querySql = $sql;
        }

        $queryResult = false;
        if ($this->connect()) {
            $queryRows = 0;
            $queryStart = microtime(true);
            $queryResult = $this->dbConn->query($this->querySql);
            $queryConsumed = microtime(true) - $queryStart;

            if (false === $queryResult) {
                $this->fetchError();
            } else {
                $queryRows = $this->dbConn->affected_rows;
            }

            $this->runCount++;
            $this->runTime += $queryConsumed;
            $this->queryLogs[] = array(
                $this->querySql,    //query statement
                $queryConsumed,     //query consumed time(s)
                $queryResult,       //query result (true/false)
                $queryRows,         //the number of affected rows in this operation
            );
        }
        return $queryResult;
    }

    /**
     * add a new record into given table
     */
    public function insert($table, $values)
    {
        $vars = $this->filterVars($values);
        $this->querySql = "INSERT INTO {$table} SET {$vars}";
        
        $queryResult = false;
        if ($this->connect()) {
            $queryRows = 0;
            $queryStart = microtime(true);
            $queryResult = $this->dbConn->query($this->querySql);
            $queryConsumed = microtime(true) - $queryStart;

            if (false === $queryResult) {
                $this->fetchError();
            }
            else if ($this->dbConn->insert_id) {
                $queryResult = $this->dbConn->insert_id;  //new record id
                $queryRows = $this->dbConn->affected_rows;
            }
            else {
                $queryRows = $this->dbConn->affected_rows;
            }

            $this->runCount++;
            $this->runTime += $queryConsumed;
            $this->queryLogs[] = array(
                $this->querySql,    //query statement
                $queryConsumed,     //query consumed time(s)
                $queryResult,       //query result (true/false)
                $queryRows,         //new id or affected rows
            );
        }
        return $queryResult;
    }
    /**
	 * 获取结果数
	 * @param string $_sql
	 * @param array $_array
	 *
	 * @return int
	 */
	public function getrowCount($sql)
	{
        $counts=$this->exe($sql);
        return $counts->num_rows;
	}
    /**
     * update the records in given table
     */
    public function update($table, $values, $where)
    {
        $val = $this->filterVars($values);
        $this->querySql = "UPDATE {$table} SET {$val} WHERE {$where}";

        $queryResult = false;
        if ($this->connect()) {
            $queryRows = 0;
            $queryStart = microtime(true);
            $queryResult = $this->dbConn->query($this->querySql);
            $queryConsumed = microtime(true) - $queryStart;

            if (false === $queryResult) {
                $this->fetchError();
            } else {
                $queryRows = $this->dbConn->affected_rows;
            }

            $this->runCount++;
            $this->runTime += $queryConsumed;
            $this->queryLogs[] = array(
                $this->querySql,    //query statement
                $queryConsumed,     //query consumed time(s)
                $queryResult,       //query result (true/false)
                $queryRows,         //affected rows
            );
        }
        return $queryResult;
    }

    
    /**
     * delete the records in given table
     */
    public function delete($table, $where)
    {
        $this->querySql = "DELETE LOW_PRIORITY FROM {$table} {$where}";

        $queryResult = false;
        if ($this->connect()) {
            $queryRows = 0;
            $queryStart = microtime(true);
            $queryResult = $this->dbConn->query($this->querySql);
            $queryConsumed = microtime(true) - $queryStart;

            if (false === $queryResult) {
                $this->fetchError();
            } else {
                $queryRows = $this->dbConn->affected_rows;
            }

            $this->runCount++;
            $this->runTime += $queryConsumed;
            $this->queryLogs[] = array(
                $this->querySql,    //query statement
                $queryConsumed,     //query consumed time(s)
                $queryResult,       //query result (true/false)
                $queryRows,         //affected rows
            );
        }
        return $queryResult;
    }

    /**
     * whether there is an error
     */
    public function hasError()
    {
        return $this->errNo > 0;
    }

    /**
     * return the error message
     */
    public function getError()
    {
        return $this->errMsg;
    }

    /**
     * return the query history
     */
    public function getLogs()
    {
        return $this->queryLogs;
    }

    /**
     * create/check mysql connection
     */
    private function connect()
    {
        $living = true;
        if (!$this->dbConn || !$this->dbConn->ping()) {
            $this->dbConn = new mysqli($this->dbHost, $this->dbUser, $this->dbUpwd, $this->dbName);
            if ($this->dbConn->connect_errno) {
                $this->fetchError($this->dbConn->connect_errno, $this->dbConn->connect_error);
                $living = false;
            }
            else if (!$this->dbConn->set_charset($this->dbChar) || !$this->dbConn->select_db($this->dbName) || !$this->dbConn->autocommit(true)) {
                $this->fetchError();
                $living = false;
            }
        }
        return $living;
    }

    /**
     * filter variables
     */
    private function filterVars($vars)
    {
        $arr = array();
        foreach ($vars as $k => $v) {
            if ('=' == substr($v,0,1) && preg_match('/^[\w\+\-\*\/\._,()\s]+$/', substr($v,1))) {
                $arr[] = $k . $v;
            } else {
                $arr[] = $k . "='" . $this->escape($v) . "'";
            }
        }
        return implode(',', $arr);
    }

    /**
     * fetch args to generate sql statement
     */
    private function fetchArgs($args)
    {
        $num = sizeof($args);
        $arr = array();
        for ($i = 1; $i < $num; $i++) {
            $arr['#'.$i] = $this->escape($args[$i]);
        }
        return strtr(trim($args[0]), $arr);
    }
    

    /**
     * fetch result into array
     */
    private function fetchResult($type = MYSQLI_ASSOC, $singleRow = false)
    {
        $result = array();
        if ('SELECT' == strtoupper(substr($this->querySql, 0, 6))) {
            if ($this->connect()) {
                $queryRows = 0;
                $queryStart = microtime(true);
                $queryResult = $this->dbConn->query($this->querySql);
                $queryConsumed = microtime(true) - $queryStart;

                if (false !== $queryResult) {
                    if ($singleRow) {
                        $result = $queryResult->fetch_array($type);
                        $queryRows = 1;
                    } else {
                        while ($row = $queryResult->fetch_array($type)) {
                            $result[] = $row;
                            $queryRows++;
                        }
                    }
                    $queryResult->free();
                    $queryResult = true;
                }
                else if ($this->dbConn->errno) {
                    $this->fetchError();
                }

                $this->runCount++;
                $this->runTime += $queryConsumed;
                $this->queryLogs[] = array(
                    $this->querySql,    //query statement
                    $queryConsumed,     //query consumed time(s)
                    $queryResult,       //query result (true/false)
                    $queryRows,         //records count
                );
            }
        }
        else {
            $this->fetchError(100, 'wrong query statement');
        }
        return $result;
    }

    /**
     * catch a error
     */
    private function fetchError($errno = null, $error = null)
    {
        if (is_null($errno)) {
            $this->errNo = $this->dbConn->errno;
        }
        if (is_null($error)) {
            $this->errMsg = $this->dbConn->error;
        }
    }
}

