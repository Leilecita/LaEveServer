<?php

include 'models/OrderModel.php';
include 'models/ItemOrderModel.php';


$model= new OrderModel();
$items_order= new ItemOrderModel();


$filter=array();

//$previous_date = date('Y-m-d', strtotime(  date("Y-m-d").' -4 day'))." 00:00:00";

$filter[]='created <= "' ."2023-06-01 23:10:10".'"';

$orders = $model->findAllAll($filter);


for ($i = 0; $i < count($orders); ++$i) {

    $items_order->deleteAllByOrderId($orders[$i]['id']);
    $model->delete($orders[$i]['id']);

}




