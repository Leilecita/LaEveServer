<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 26/07/2019
 * Time: 13:56
 */
require_once 'BaseModel.php';

class ZoneModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'zones';
    }

    function getZones(){
        $query = 'SELECT DISTINCT name FROM zones ORDER BY name DESC ';
        return $this->getDb()->fetch_all($query);
    }
}