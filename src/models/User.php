<?php

require_once __DIR__ . '/../../config/database.php';

class User {

    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    /* Sprawdza czy email istnieje */
    public function emailExists(string $email): bool {

        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        return $stmt->fetch() !== false;
    }

    /* Rejestracja */
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

    /* Logowanie */
    public function login(string $email, string $password){

        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password_hash'])){
            return $user;
        }

        return false;
    }
}
