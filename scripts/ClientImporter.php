<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 23/05/2019
 * Time: 09:37
 */


require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../models/ClientModel.php';

use XBase\Table;

$clientModel = new ClientModel();


$table = // new Table(dirname(__FILE__).'/clientes.dbf');

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
        $s['id'] = $s['codcli'];
        unset($s['codcli']);
        $clientModel->save($s);
    }catch(Exception $e){
        echo 'Error: '.print_r($s,true);
    }

}