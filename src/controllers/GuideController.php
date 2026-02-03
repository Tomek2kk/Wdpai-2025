<?php

require_once __DIR__ . '/../models/Guide.php';

class GuideController {

    private Guide $guide;

    public function __construct(){
        $this->guide = new Guide();
    }

    /* Lista */
    public function index(){

        $guides = $this->guide->getAll();

        require ROOT_PATH.'/src/views/guides/list.php';
    }

    /* Dodawanie */
    public function create(){

        $this->auth();
        if($_SERVER['REQUEST_METHOD']==='POST'){

        $title = $_POST['title'];
        $content = $_POST['content'];

        $imageName = null;

        if(!empty($_FILES['image']['name'])){

            $ext = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);

            $imageName = uniqid().".".$ext;

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                ROOT_PATH."/public/uploads/".$imageName
            );
        }

        $this->guide->create(
            $title,
            $content,
            $imageName,
            $_SESSION['user_id']
        );

        header("Location: /");
        exit;
        }
    }


    /* Edycja */
    public function edit(){

        $this->auth();

        $id = $_GET['id'] ?? null;

        if(!$id) die("Brak ID");

        if(!$this->guide->isOwner($id,$_SESSION['user_id'])){
            die("Brak dostępu");
        }

        $guide = $this->guide->getById($id);

        if($_SERVER['REQUEST_METHOD']==='POST'){

            $title = trim($_POST['title']);
            $desc  = trim($_POST['description']);

            $this->guide->update($id,$title,$desc);

            header("Location: /");
            exit;
        }

        require ROOT_PATH.'/src/views/guides/edit.php';
    }

    /* Usuwanie */
    public function delete(){

        $this->auth();

        $id = $_GET['id'] ?? null;

        if(!$id) die("Brak ID");

        if(!$this->guide->isOwner($id,$_SESSION['user_id'])){
            die("Brak dostępu");
        }

        $this->guide->delete($id);

        header("Location: /");
    }

    /* Sprawdź logowanie */
    private function auth(){

        if(!isset($_SESSION['user_id'])){
            header("Location: /?action=login");
            exit;
        }
    }

    /* Widok pojedynczego */
    public function show(){
        $id = $_GET['id'] ?? null;
        if(!$id) die("Brak ID");
        $guide = $this->guide->getById($id);
        if(!$guide) die("Nie istnieje");
        require ROOT_PATH.'/src/views/guides/show.php';
    }

}
