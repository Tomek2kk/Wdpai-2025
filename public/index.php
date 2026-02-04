<?php

session_start();

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH.'/config/database.php';

require_once ROOT_PATH.'/src/helpers/View.php';
require_once ROOT_PATH.'/src/helpers/Auth.php';

require_once ROOT_PATH.'/src/controllers/AuthController.php';
require_once ROOT_PATH.'/src/controllers/GuideController.php';
require_once ROOT_PATH.'/src/controllers/CommentController.php';
require_once ROOT_PATH.'/src/controllers/RatingController.php';
require_once ROOT_PATH.'/src/controllers/UserAdminController.php';

$action = $_GET['action'] ?? '';


switch($action){

    case '':
    case 'home':
        (new GuideController)->index();
        break;

    case 'login':
        (new AuthController)->login();
        break;

    case 'register':
        (new AuthController)->register();
        break;

    case 'logout':
        (new AuthController)->logout();
        break;

    case 'show-guide':
        (new GuideController)->show();
        break;

    case 'add-guide':
        (new GuideController)->create();
        break;

    case 'edit-guide':
        (new GuideController)->edit();
        break;

    case 'delete-guide':
        (new GuideController)->delete();
        break;

    case 'search':
        (new GuideController)->search();
        break;

    case 'api-add-comment':
        (new CommentController)->add();
        break;

    case 'api-delete-comment':
        (new CommentController)->delete();
        break;

    case 'api-rate':
        (new RatingController)->rate();
        break;

    case 'admin-users':
        (new UserAdminController)->index();
        break;

    case 'admin-delete-user':
        (new UserAdminController)->delete();
        break;

    case 'admin-toggle':
        (new UserAdminController)->toggle();
        break;

    default:

        http_response_code(404);

        echo "<h1>404 - Nie znaleziono strony</h1>";
        break;
}
