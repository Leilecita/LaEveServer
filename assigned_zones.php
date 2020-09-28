<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 28/09/2020
 * Time: 13:03
 */

include 'controllers/AssignedZonesController.php';


$controller = new AssignedZonesController();

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