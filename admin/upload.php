<?php

//var_dump($_FILES);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../models/ClientModel.php';
require_once __DIR__.'/../models/ProductModel.php';

use XBase\Table;

error_reporting(0);
$countOk=0;
$countError=0;
if (isset($_FILES["clientes"]) && !$_FILES["clientes"]["error"]){
    $clientModel = new ClientModel();


    $table = new Table($_FILES["clientes"]["tmp_name"]);

    $columns = $table->getColumns();

    while ($record = $table->nextRecord()) {
        $s = [];
        foreach ($columns as $column) {
            $s[$column->name] = iconv('CP1252', 'UTF-8',$record->forceGetString($column->name));
            if($s[$column->name] == null){
                $s[$column->name] = '';
            }
        }
        try {
            $s['id'] = $s['codcli'];
            unset($s['codcli']);
            $clientModel->save($s);
       //     echo "Guardo ".$s['id'].'<br/>';

            $countOk++;
        }catch(Exception $e){
            error_log('Error: '.print_r($s,true));
            $countError++;
        }

    }
    echo " Se crearon $countOk clientes y fallaron $countError <br/>";

}else{
    echo "error al procesar el archivo clientes<br/>";
}


$countOk=0;
$countError=0;
if (isset($_FILES["productos"]) && !$_FILES["productos"]["error"]){
    $productModel = new ProductModel();


    $table = new Table($_FILES["productos"]["tmp_name"]);

    $columns = $table->getColumns();

    while ($record = $table->nextRecord()) {

        $s = [];
        foreach ($columns as $column) {
            $s[$column->name] = iconv('CP1252', 'UTF-8', $record->forceGetString($column->name));
            if ($s[$column->name] == null) {
                $s[$column->name] = '';
            }
        }
        try {
            $productModel->save($s);
            //echo "Guardo " . $s['id'] . '<br/>';

            $countOk++;
        } catch (Exception $e) {
            error_log('Error: ' . print_r($s, true));
            $countError++;
        }
    }
    echo " Se crearon $countOk productos y fallaron $countError <br/>";

}else{
    echo "error al procesar el archivo productos<br/>";
}