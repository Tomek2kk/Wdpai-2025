<?php

require_once __DIR__.'/../../config/database.php';

class Category {

    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(){

        return $this->db
            ->query("SELECT * FROM categories")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getForGuide($guideId){

        $stmt = $this->db->prepare("
            SELECT c.id, c.name
            FROM categories c
            JOIN guide_categories gc
              ON c.id = gc.category_id
            WHERE gc.guide_id = :id
            ORDER BY c.name
        ");

        $stmt->execute([
            ':id' => (int)$guideId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setForGuide($guideId,$cats){

        $this->db->prepare("
            DELETE FROM guide_categories
            WHERE guide_id = :id
            ")->execute([
            ':id'=>(int)$guideId
        ]);


        if(empty($cats)) return;

        $stmt = $this->db->prepare("
            INSERT INTO guide_categories
            (guide_id, category_id)
            VALUES (:g,:c)
        ");

        foreach($cats as $cat){

            $stmt->execute([

                ':g'=>(int)$guideId,
                ':c'=>(int)$cat
            ]);
        }
    }

}
