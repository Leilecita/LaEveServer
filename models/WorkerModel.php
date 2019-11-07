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

}