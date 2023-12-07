<?php

include 'models/OrderModel.php';
include 'models/ItemOrderModel.php';


$model= new OrderModel();
$items_order= new ItemOrderModel();


$filter=array();

//$previous_date = date('Y-m-d', strtotime(  date("Y-m-d").' -4 day'))." 00:00:00";

$filter[]='created <= "' ."2020-07-16 23:10:10".'"';

$orders = $model->findAllAll($filter);


for ($i = 0; $i < count($orders); ++$i) {

    var_dump($items_order->deleteAllByOrderId($orders[$i]['id']));
    var_dump($model->delete($orders[$i]['id']));

}




