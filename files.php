<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 08/11/2019
 * Time: 14:29
 */
include 'controllers/FilesController.php';


$controller = new FilesController();

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