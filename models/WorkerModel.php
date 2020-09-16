<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 26/07/2019
 * Time: 13:56
 */
require_once 'BaseModel.php';
class WorkerModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'workers';
    }

    function findAllWorkers($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC';
        return $this->getDb()->fetch_all($query);
    }

    function getUsersJoinWorkers($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT *, w.name as worker_name, w.id as worker_id, u.id as user_id FROM workers w JOIN users u ON w.id = u.worker_id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY w.created DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->getDb()->fetch_all($query);
    }

}