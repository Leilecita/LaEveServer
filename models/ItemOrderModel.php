<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 15:32
 */
require_once 'BaseModel.php';
class ItemOrderModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'items_order';
    }


    function getItemsOrder($filters=array(),$paginator=array(),$order_state){

        $conditions = join(' AND ',$filters);
        //$query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        $query = 'SELECT *, o.id as order_id FROM items_order i JOIN products p ON i.product_id = p.codart '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->getDb()->fetch_all($query);

    }

    function countItemsLoaded($loaded,$order_id){

        $response = $this->getDb()->fetch_row('SELECT COUNT(id) AS total FROM '.$this->tableName.' WHERE loaded = ? AND order_id = ?',$loaded,$order_id);

        if($response['total']!=null){
            return $response['total'];
        }else{
            $response['total']=0;
            return $response['total'];
        }
    }
}