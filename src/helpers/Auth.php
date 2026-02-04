<?php

class Auth {

    public static function check(){

        return isset($_SESSION['user_id']);
    }

    public static function role(){

        return $_SESSION['role'] ?? null;
    }

    public static function requireRole($roles){

        if(!self::check()){
            header("Location: /?action=login");
            exit;
        }

        if(!in_array(self::role(), $roles)){
            die("Brak uprawnień");
        }
    }

    public static function isAdmin(){
        return self::role() == 3;
    }

    public static function isModerator(){
        return self::role() == 2;
    }

    public static function isUser(){
        return self::role() == 1;
    }

    public static function requireApiLogin(){

    if(!self::check()){

        header('Content-Type: application/json');

        echo json_encode([
            'error' => 'NOT_LOGGED'
        ]);

        exit;
        }
    }      
}
