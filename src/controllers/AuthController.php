<?php

require_once __DIR__ . '/../models/User.php';

class AuthController {

    private User $user;

    public function __construct(){
        $this->user = new User();
    }

    public function login(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $result = $this->user->login($email,$password);

            if($result){

                $_SESSION['user_id']  = $result['id'];
                $_SESSION['role']     = $result['role_id'];
                $_SESSION['username'] = $result['username'];


                header("Location: /");
                exit;
            }

            $error = "Nieprawidłowy email lub hasło";
        }

        View::render('auth/login',[
            'title'=>'Logowanie',
            'error'=>$error ?? ''
        ]);

    }

    public function register(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $username = trim($_POST['username']);
            $email    = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm = $_POST['confirm'] ?? '';

            if(strlen($username) < 3){
                $error = "Login min. 3 znaki";
            }
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error = "Nieprawidłowy email";
            }
            elseif(strlen($password) < 6){
                $error = "Hasło min. 6 znaków";
            }
            elseif($password !== $confirm){
                $error = "Hasła nie są takie same";
            }
            elseif(!$this->user->register($username,$email,$password)){
                $error = "Email już istnieje";
            }
            else{
                header("Location: /?action=login");
                exit;
            }
        }

        View::render('auth/register',[
            'title'=>'Rejestracja',
            'error'=>$error ?? ''
        ]);

    }

    public function logout(){
        session_destroy();

        header("Location: /");
    }
}
