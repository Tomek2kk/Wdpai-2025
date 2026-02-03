<?php

require_once __DIR__ . '/../../config/database.php';

class Guide {

    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(){

        $stmt = $this->db->query("
            SELECT id,title,image,created_at
            FROM guides
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){

        $stmt = $this->db->prepare("
            SELECT g.*, u.username
            FROM guides g
            JOIN users u ON g.user_id=u.id
            WHERE g.id=?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($title,$content,$image,$userId){

        $stmt = $this->db->prepare("
            INSERT INTO guides(title,content,image,user_id)
            VALUES (?,?,?,?)
        ");

        return $stmt->execute([
            $title,
            $content,
            $image,
            $userId
        ]);
    }

    public function update($id,$title,$content,$image){

        $stmt = $this->db->prepare("
            UPDATE guides
            SET title=?, content=?, image=?
            WHERE id=?
        ");

        return $stmt->execute([
            $title,
            $content,
            $image,
            $id
        ]);
    }

    public function delete($id){

        $stmt = $this->db->prepare(
            "DELETE FROM guides WHERE id=?"
        );

        return $stmt->execute([$id]);
    }

    public function isOwner($id,$userId){

        $stmt = $this->db->prepare("
            SELECT id FROM guides
            WHERE id=? AND user_id=?
        ");

        $stmt->execute([$id,$userId]);

        return $stmt->fetch() !== false;
    }
}
