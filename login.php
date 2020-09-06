<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 20/08/2020
 * Time: 20:10
 */


include 'controllers/LoginController.php';


$controller = new LoginController();

$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'GET':
        $controller->get();
        break;
    case 'POST':
        $controller->post();
        break;
    case 'DELETE':
        $controller->delete();
        break;
    case 'PUT':
        $controller->put();
        break;
    default:
        $controller->returnError(400,'INVALID METHOD');
        break;
}