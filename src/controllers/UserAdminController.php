<?php

require_once ROOT_PATH.'/src/models/User.php';
require_once ROOT_PATH.'/src/helpers/Auth.php';
require_once ROOT_PATH.'/src/helpers/View.php';


class UserAdminController {

    public function index(){

        Auth::requireRole([3]); 


        $model = new User();

        $users = $model->getAll();


        $rows = '';


        foreach($users as $u){


            $status = $u['is_active']
                ? '<span style="color:green">Aktywny</span>'
                : '<span style="color:red">Zablokowany</span>';



            if($u['is_active'])
            {
                $blockBtn = "
                <a href='/?action=admin-toggle&id={$u['id']}'
                   class='btn btn-delete'>
                   Zablokuj
                </a>";
            }
            else{
                $blockBtn = "
                <a href='/?action=admin-toggle&id={$u['id']}'
                   class='btn btn-comment'>
                   Odblokuj
                </a>";
            }

            if($u['id'] != $_SESSION['user_id']){

                $deleteBtn = "
                <a href='/?action=admin-delete-user&id={$u['id']}'
                   class='btn btn-delete'
                   onclick=\"return confirm('Usunąć użytkownika?')\">
                   Usuń
                </a>";
            }
            else{
                $deleteBtn = "<span>-</span>";
            }


            $rows .= "
            <tr>

                <td>{$u['id']}</td>
                <td>{$u['username']}</td>
                <td>{$u['email']}</td>
                <td>{$u['role_name']}</td>
                <td>$status</td>

                <td>
                    $blockBtn
                    $deleteBtn
                </td>

            </tr>
            ";
        }


        View::render('admin/users',[

            'title' => 'Użytkownicy',

            'users' => $rows,

            'error' => ''
        ]);
    }
    
    public function toggle(){

        Auth::requireRole([3]);


        $id = $_GET['id'] ?? null;

        if(!$id){
            die("Brak ID");
        }

        if($id == $_SESSION['user_id']){
            die("Nie możesz zablokować własnego konta");
        }


        $model = new User();

        $user = $model->getById($id);


        if(!$user){
            die("Nie istnieje");
        }

        $new = $user['is_active'] ? 0 : 1;


        $model->setActive($id,$new);

        header("Location: /?action=admin-users");
        exit;
    }

    public function delete(){

        Auth::requireRole([3]);


        $id = $_GET['id'] ?? null;

        if(!$id){
            die("Brak ID");
        }

        if($id == $_SESSION['user_id']){
            die("Nie możesz usunąć własnego konta");
        }


        $model = new User();

        $user = $model->getById($id);


        if(!$user){
            die("Nie istnieje");
        }

        if($user['role_id'] == 3){

            if($model->countAdmins() <= 1){
                die("Nie można usunąć ostatniego administratora");
            }
        }


        $model->delete($id);


        header("Location: /?action=admin-users");
        exit;
    }

}
