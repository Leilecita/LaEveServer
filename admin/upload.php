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

    $columnasProd = array("id","nomcli","comcli","dircli","telcli","loccli","celcli");


    $table = new Table($_FILES["clientes"]["tmp_name"]);

    $columns = $table->getColumns();

    while ($record = $table->nextRecord()) {
        $s = [];
        foreach ($columns as $column) {
            if(in_array($column->name,$columnasProd)) {
                $s[$column->name] = iconv('CP1252', 'UTF-8', $record->forceGetString($column->name));
                // echo "Guardo ".$s[$column->name].'<br/>';
                if ($s[$column->name] == null) {
                    $s[$column->name] = '';
                }
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

  //  $columnasProd = array("id","codart","desart","codbar","ubicac","oferta","cosart","descu1","descu2","descu3","descu4","cosfin","gasfle","gastos","cosrea","poriva","marge1","marge2","marge3","marge4","marge5","preci1","preci2","preci3","preci4","preci5","preci6","stocka","stomin","conten","desabr","moduni","modfin","codrub","codsub","codpro","codmar","fecalt","fecpre","marcas","concom","tipcos","consto","fecref","moddes","presen","mensaj","codbar2","codbar3","exeiva");
    $columnasProd = array("id","codart","desart","preci1","preci2","preci3","preci4","preci5");

    $table = new Table($_FILES["productos"]["tmp_name"]);

    $columns = $table->getColumns();

    while ($record = $table->nextRecord()) {

        $s = [];
        foreach ($columns as $column) {
            if(in_array($column->name,$columnasProd)){
                $s[$column->name] = iconv('CP1252', 'UTF-8', $record->forceGetString($column->name));
                if ($s[$column->name] == null) {
                    $s[$column->name] = '';
                }
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


/*
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
            echo 'colum: '.print_r($s[$column->name],true);
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

*/
