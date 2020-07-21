<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 26/07/2019
 * Time: 13:55
 */

require_once 'BaseController.php';
require_once __DIR__.'/../models/ZoneModel.php';
class ZonesController extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->model = new ZoneModel();
    }

    function getZones(){
        $this->returnSuccess(200,$this->model->getZones());
    }
}