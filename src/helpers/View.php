<?php

class View {

    public static function render($view,$data=[]){

        $viewPath =
            ROOT_PATH.'/src/views/'.$view.'.html';

        $layoutPath =
            ROOT_PATH.'/src/views/layout/base.html';


        if(!file_exists($viewPath)){
            die("Brak widoku: ".$view);
        }

        $html = file_get_contents($viewPath);
        $layout = file_get_contents($layoutPath);

        foreach($data as $key=>$value){

            if(is_array($value)) continue;

            $html = str_replace(
                '{{'.$key.'}}',
                $value,
                $html
            );
        }

        $menu = self::menu();

        $layout = str_replace('{{content}}',$html,$layout);
        $layout = str_replace('{{menu}}',$menu,$layout);
        $layout = str_replace(
            '{{title}}',
            $data['title'] ?? 'AutoFix',
            $layout
        );

        echo $layout;
    }


    private static function menu(){

        if(!isset($_SESSION['user_id'])){

            return '
                <a href="/?action=login">Login</a>
                <a href="/?action=register">Rejestracja</a>
            ';
        }


        $name = $_SESSION['username'] ?? 'Użytkownik';

        $out = '<span>'.$name.'</span>';


        if($_SESSION['role']==3){
            $out .= ' <a href="/?action=admin-users">Admin</a>';
        }

        $out .= ' <a href="/?action=logout">Wyloguj</a>';

        return $out;
    }
}
