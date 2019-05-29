<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 15:25
 */
require_once 'BaseModel.php';
class OrderModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'orders';
    }

    function count($delivery_date,$state){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND state = ?',$delivery_date,$state);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }


    function countDelivery($delivery_date,$state){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND state_delivery = ?',$delivery_date,$state);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }

    function countPrepare($delivery_date,$state){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND state_prepare = ?',$delivery_date,$state);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }

    function countCheck($delivery_date,$state){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND state_check = ?',$delivery_date,$state);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }
}