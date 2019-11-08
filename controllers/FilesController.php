<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 08/11/2019
 * Time: 14:29
 */


require_once 'BaseController.php';
require_once __DIR__.'/../models/FileModel.php';
class FilesController extends BaseController
{

    function __construct(){
        parent::__construct();
        $this->model = new FileModel();
    }



    function getFilesByType(){

        $resClients=$this->model->findFilesByType(array('name = "clientes"'));
        $resProducts=$this->model->findFilesByType(array('name = "productos"'));

        $client=array('name' => "", 'ok' => 1, 'failed' => 1);
        $product=array('name' => "", 'ok' => 1, 'failed' => 1);

        if(count($resClients)>0){
            $client=$resClients[0];
        }

        if(count($resProducts)>0){
            $product=$resProducts[0];
        }

        $res=array('client'=> $client, 'product' => $product);
        $this->returnSuccess(200,$res);
    }

}