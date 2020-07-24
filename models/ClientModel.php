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

    function getLocalities(){

        $query = 'SELECT DISTINCT loccli FROM clients ORDER BY loccli DESC ';
        return $this->getDb()->fetch_all($query);

    }

    function countPendientOrdersByClientId($client_id){
        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM orders WHERE state_prepare = ? AND state_delivery = ? AND client_id = ?',"toprepare","todelivery",$client_id);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }

    }

    function findAllByNameCli($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY nomcli ASC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->getDb()->fetch_all($query);
    }

}
