<?php

require_once ROOT_PATH.'/src/models/Rating.php';
require_once ROOT_PATH.'/src/helpers/Auth.php';

class RatingController {

    public function rate(){

        header('Content-Type: application/json');

        Auth::requireApiLogin();

        $data = json_decode(file_get_contents("php://input"),true);

        $rating = $data['rating'];
        $guide  = $data['guide'];

        $model = new Rating();

        $model->rate(
            $_SESSION['user_id'],
            $guide,
            $rating
        );

        echo json_encode(['success'=>true]);
    }
}
