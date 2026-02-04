<?php

require_once __DIR__.'/../../config/database.php';

class Rating {

    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function rate($user,$guide,$value){

        $stmt = $this->db->prepare("
            INSERT INTO ratings(user_id,guide_id,rating)
            VALUES (?,?,?)
            ON CONFLICT (user_id,guide_id)
            DO UPDATE SET rating=EXCLUDED.rating
        ");

        return $stmt->execute([$user,$guide,$value]);
    }

    public function getAverage($guide){

        $stmt = $this->db->prepare("
            SELECT ROUND(AVG(rating),1) AS avg
            FROM ratings
            WHERE guide_id=?
        ");

        $stmt->execute([$guide]);

        return $stmt->fetchColumn();
    }
}
