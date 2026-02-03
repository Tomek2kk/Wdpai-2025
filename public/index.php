<?php

session_start();

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH.'/src/controllers/AuthController.php';
require_once ROOT_PATH.'/src/controllers/GuideController.php';

$action = $_GET['action'] ?? 'home';

$auth  = new AuthController();
$guide = new GuideController();

switch($action){

    case 'login':
        $auth->login();
        break;

    case 'register':
        $auth->register();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'create-guide':
        $guide->create();
        break;

    case 'edit-guide':
        $guide->edit();
        break;

    case 'delete-guide':
        $guide->delete();
        break;

    case 'show-guide':
        $guide->show();
        break;

    default:
        $guide->index();   
}
