<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 14:54
 */
require_once __DIR__.'/SecureBaseController.php';
require_once  __DIR__.'/../models/UserModel.php';
class UsersController extends SecureBaseController
{
    function __construct(){
        parent::__construct();
        $this->model = new UserModel();
    }


    public function get()
    {
        $this->beforeMethod();

        $user= $this->getCurrentUser();
        $this->returnSuccess(200,array("token" => $user['token']));
    }

    function getAllUsers(){
        $this->returnSuccess(200,$this->getModel()->findAllAll($this->getFilters()));

    }

}