<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 14:47
 */

require_once 'BaseController.php';
require_once __DIR__.'/../libs/SessionHelper.php';
abstract class SecureBaseController extends BaseController
{
    protected $currentUser;

    function __construct()
    {
        $this->currentUser = null;
    }

    function beforeMethod()
    {
        $this->_checkSession();
    }

    function _checkSession(){
       $this->currentUser = SessionHelper::getCurrentUser();
        if($this->getCurrentUser() == null) {
            error_log("checksession null");
            $this->returnError(401,'Session invalida');
            exit;
        }
    }

    function getCurrentUser(){
        return $this->currentUser;
    }
}