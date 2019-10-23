<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 23/05/2019
 * Time: 09:34
 */
require_once 'BaseModel.php';
class ClientModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'clients';
    }


    function deleteAll(){
        //$query='DELETE FROM '.$this->tableName;
        $query='TRUNCATE TABLE '.$this->tableName;
        return $this->getDb()->fetch_row($query);
    }

}
