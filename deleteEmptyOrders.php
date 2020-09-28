<?php

include 'models/OrderModel.php';


$model= new OrderModel();


$filter=array();

//$previous_date = date('Y-m-d', strtotime(  date("Y-m-d").' -4 day'))." 00:00:00";

$filter[]='delivery_date = "' ."1999-10-10".'"';

$listPendients= $model->deleteEmptyOrders($filter);

