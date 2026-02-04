<?php

require_once __DIR__ . '/../../config/database.php';

class User {

    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function emailExists(string $email): bool {

        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        return $stmt->fetch() !== false;
    }

    public function register(
        string $username,
        string $email,
        string $password
    ): bool {

        if($this->emailExists($email)){
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO users
            (username,email,password_hash,role_id)
            VALUES (?,?,?,1)
        ");

        return $stmt->execute([
            $username,
            $email,
            $hash
        ]);
    }

    public function login(string $email, string $password){

        $stmt = $this->db->prepare("
        SELECT * FROM users
        WHERE email = ? AND is_active = TRUE
        ");


        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password_hash'])){
            return $user;
        }

        return false;
    }

    public function getById($id){

        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => (int)$id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll(){

    $stmt = $this->db->query("
        SELECT u.*, r.name AS role_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
        ORDER BY u.id
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function changeRole($id,$roleId){

    $stmt = $this->db->prepare("
        UPDATE users SET role_id=? WHERE id=?
    ");

    return $stmt->execute([$roleId,$id]);
    }

    public function deleteUser($id){

    $stmt = $this->db->prepare("
        DELETE FROM users WHERE id=?
    ");

    return $stmt->execute([$id]);
    }

    public function setActive($id,$status){

    $stmt = $this->db->prepare("
        UPDATE users
        SET is_active = :a
        WHERE id = :id
    ");

    $stmt->execute([
        ':a'  => (int)$status,
        ':id' => (int)$id
    ]);
    }


    public function countAdmins(){

    $stmt = $this->db->query("
        SELECT COUNT(*) FROM users
        WHERE role_id = 3
    ");

    return (int)$stmt->fetchColumn();
    }

}
