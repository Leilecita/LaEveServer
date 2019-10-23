<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 14/05/2019
 * Time: 13:28
 */

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../models/ProductModel.php';

use XBase\Table;

$productModel = new ProductModel();


$table = new Table(dirname(__FILE__).'/test.dbf');

$columns = $table->getColumns();

while ($record = $table->nextRecord()) {
    $s = [];
    foreach ($columns as $column) {
        $s[$column->name] = iconv('CP1252', 'UTF-8',$record->forceGetString($column->name));
        echo 'colum: '.print_r($s[$column->name],true);
        if($s[$column->name] == null){
            $s[$column->name] = '';
        }
    }
    try {
        $productModel->save($s);
    }catch(Exception $e){
        echo 'Error: '.print_r($s,true);
    }
}