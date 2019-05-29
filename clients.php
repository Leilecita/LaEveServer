<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 22/05/2019
 * Time: 17:40
 */
include 'controllers/ClientsController.php';


$controller = new ClientsController();

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