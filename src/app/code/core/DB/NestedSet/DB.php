<?php

// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet_DB                                              |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Daniel Khan <dk@webcluster.at>                              |
// +----------------------------------------------------------------------+

// $Id: DB.php 244556 2007-10-21 20:17:27Z datenpunk $

require_once 'DB.php';
// {{{ DB_NestedSet_DB:: class
/**
 * Wrapper class for PEAR::DB
 *
 * @author Daniel Khan <dk@webcluster.at>
 * @package DB_NestedSet
 * @version $Revision: 244556 $
 * @access public
 */
// }}}
class DB_NestedSet_DB extends DB_NestedSet {
    // {{{ properties
    /**
     *
     * @var object Db object
     */
    var $db;
    // }}}
    // {{{ constructor
    /**
     * Constructor
     *
     * @param mixed $dsn DSN as PEAR dsn URI or dsn Array
     * @param array $params Database column fields which should be returned
     */
    public function __construct($dsn, $params = array())
    {
        $this->_debugMessage('__construct($dsn, $params = array())');
        parent::__construct($params);
        $this->db = & $this->_db_Connect($dsn);
        if ($this->_isDBError($this->db)) {
            return false;
        }
        $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
    }
    // }}}
    // {{{ destructor
    /**
     * Destructor
     */
    public function __destruct() 
    {
        $this->_debugMessage('__destruct()');
        parent::__destruct();
        $this->_db_Disconnect();
    }
    // }}}
    // {{{ _db_Connect()
    /**
     * Connects to the db
     *
     * @return object DB The database object
     * @access private
     */
    protected function & _db_Connect($dsn) {
        $this->_debugMessage('_db_Connect($dsn)');
        if (DB::isConnection($this->db)) {
            return $this->db;
        }
        if (DB::isConnection($dsn)) {
            return $dsn;
        }
        $db = & DB::connect($dsn);
        $this->_testFatalAbort($db, __FILE__, __LINE__);
        return $db;
    }
    // }}}
    // {{{ _numRows()
    protected function _numRows($res) {
        return $res->numRows();
    }
    // }}}
    // {{{ _isDBError()
    protected function _isDBError($err) {
        if (!DB::isError($err)) {
            return false;
        }
        return true;
    }
    // }}}
    // {{{ _query()
    protected function _query($sql) {
        return $this->db->query($sql);
    }
    // }}}
    // {{{ _quote()
    protected function _quote($str) {
        if (method_exists($this->db, 'quoteSmart')) {
            return $this->db->quoteSmart($str);
        }
        return $this->db->quote($str);
    }
    // }}}
    // {{{ _quoteIdentifier()
    protected function _quoteIdentifier($str) {
        if (method_exists($this->db, 'quoteIdentifier')) {
            return $this->db->quoteIdentifier($str);
        }
        return $this->_quote($str);
    }
    // }}}
    // {{{ _dropSequence()
    protected function _dropSequence($sequence) {
        return $this->db->dropSequence($this->sequence_table);
    }
    // }}}
    // {{{ _nextId()
    protected function _nextId($sequence) {
        return $this->db->nextId($sequence);
    }
    // }}}

    // {{{ _getOne()

    /**
     * @param string $sql SQL query
     * @return mixed
     * @access private
     */
    protected function _getOne($sql)
    {
        return $this->db->getOne($sql);
    }

    // }}}

    // {{{ _getAll()
    protected function _getAll($sql) {
        return $this->db->getAll($sql, null, DB_FETCHMODE_ASSOC);
    }
    // }}}
    // {{{ _db_Disconnect()
    /**
     * Disconnects from db
     *
     * @return void
     * @access private
     */
    protected function _db_Disconnect() {
        $this->_debugMessage('_db_Disconnect()');
        if (is_object($this->db)) {
            @$this->db->disconnect();
        }

        return true;
    }
    // }}}
}