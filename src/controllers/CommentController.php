<?php

require_once ROOT_PATH.'/src/models/Comment.php';
require_once ROOT_PATH.'/src/helpers/Auth.php';


class CommentController {

    public function add(){

        header('Content-Type: application/json');


        if(!Auth::check()){

            echo json_encode([
                'success'=>false,
                'error'=>'Brak dostępu'
            ]);
            return;
        }


        $data = json_decode(
            file_get_contents('php://input'),
            true
        );


        if(!$data){

            echo json_encode([
                'success'=>false,
                'error'=>'Brak danych'
            ]);
            return;
        }


        $guideId = $data['guide_id'] ?? null;
        $content = trim($data['content'] ?? '');


        if(!$guideId || empty($content)){

            echo json_encode([
                'success'=>false,
                'error'=>'Puste dane'
            ]);
            return;
        }


        $model = new Comment();

        $model->create(
            $guideId,
            $_SESSION['user_id'],
            $content
        );


        echo json_encode([
            'success'=>true
        ]);
    }

    public function delete(){

    header('Content-Type: application/json');


    if(!Auth::check()){

        echo json_encode([
            'success' => false,
            'error'   => 'Brak dostępu'
        ]);
        return;
    }


    $data = json_decode(
        file_get_contents('php://input'),
        true
    );


    if(!$data || !isset($data['id'])){

        echo json_encode([
            'success' => false,
            'error'   => 'Brak ID'
        ]);
        return;
    }


    $id = (int)$data['id'];


    $model = new Comment();

    $comment = $model->getById($id);


    if(!$comment){

        echo json_encode([
            'success' => false,
            'error'   => 'Nie istnieje'
        ]);
        return;
    }


    $userId = $_SESSION['user_id'] ?? 0;

    if(
        Auth::isAdmin() ||
        Auth::isModerator() ||
        $comment['user_id'] == $userId
    ){

        $model->delete($id);

        echo json_encode([
            'success' => true
        ]);
    }
    else{

        echo json_encode([
            'success' => false,
            'error'   => 'Brak uprawnień'
        ]);
    }
    }

}
