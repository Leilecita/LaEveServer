<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 28/09/2020
 * Time: 13:04
 */

require_once 'BaseController.php';
require_once __DIR__.'/SecureBaseController.php';
require_once __DIR__.'/../models/AssignedZoneModel.php';

class AssignedZonesController extends SecureBaseController
{
    function __construct(){
        parent::__construct();
        $this->model = new AssignedZoneModel();
    }


}