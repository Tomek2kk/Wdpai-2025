<?php

class AdminController {

    public function dashboard(){

        Auth::requireRole([3]);

        require ROOT_PATH.'/src/views/admin/dashboard.php';
    }
}
