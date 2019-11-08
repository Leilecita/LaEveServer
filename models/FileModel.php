<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 08/11/2019
 * Time: 14:30
 */

require_once 'BaseModel.php';
class FileModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'files';
    }

    function findFilesByType($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC';
        return $this->getDb()->fetch_all($query);
    }

}