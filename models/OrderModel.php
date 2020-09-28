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

        //$response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND state_delivery = ?',$delivery_date,$state);
       // $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND state_delivery = ? AND state_billing = ?',$delivery_date,$state,"billing");
        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE state_delivery = ? AND state_billing = ?',$state,"billing");

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }

    function countPrepare($delivery_date,$state){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE state_prepare = ? AND state_check = ? AND delivery_date != ?',$state,"check","1999-10-10");

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

    function countBilling($delivery_date,$state){

       // $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND state_billing = ? AND state_prepare = ?',$delivery_date,$state,"prepare");
        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE state_billing = ? AND state_prepare = ?',$state,"prepare");

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }

    function sumPaidAmountByWorker($delivery_date,$worker_name,$delivery){

        $response = $this->getDb()->fetch_row('SELECT SUM(paid_amount) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND delivery_by = ? AND state_delivery = ?',$delivery_date,$worker_name,$delivery);
        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0.0;
            return $response['total'];
        }
    }

    function sumTotalAmount($delivery_date,$worker_name,$delivery){

        $response = $this->getDb()->fetch_row('SELECT SUM(total_amount) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND delivery_by = ? AND state_delivery = ?',$delivery_date,$worker_name,$delivery);
        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0.0;
            return $response['total'];
        }
    }


    function countDeliveryOrders($delivery_date,$worker_name,$delivery){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND delivery_by = ? AND state_delivery = ?',$delivery_date,$worker_name,$delivery);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }

    function countLoadOrders($delivery_date,$worker_name,$delivery,$loaded_in){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND loaded_by = ? AND state_delivery = ? AND loaded_in = ?',$delivery_date,$worker_name,$delivery,$loaded_in);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }

    function countTotalLoadOrders($delivery_date,$worker_name,$delivery){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE delivery_date = ? AND loaded_by = ? AND state_delivery = ?',$delivery_date,$worker_name,$delivery);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }

    function getOrdersClient($filters=array(),$paginator=array(),$order_state){

        $conditions = join(' AND ',$filters);
        //$query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC, o.created DESC, o.state_prepare DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        $query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC, o.delivery_date ASC, o.state_prepare DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->getDb()->fetch_all($query);

    }


    function getAllOrders($filters=array(),$paginator=array()){

        $conditions = join(' AND ',$filters);
       // $query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC, o.created DESC , o.state_prepare DESC
        $query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.delivery_date DESC
        LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->getDb()->fetch_all($query);

    }

    function getTheFirstOfList($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC';
        return $this->getDb()->fetch_all($query);
    }
}