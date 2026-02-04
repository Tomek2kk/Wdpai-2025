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

    public function create($title,$content,$image,$user){

    $stmt = $this->db->prepare("
        INSERT INTO guides
        (title, content, image, user_id, created_at)
        VALUES (:t,:c,:i,:u,NOW())
    ");

    $stmt->execute([

        ':t'=>$title,
        ':c'=>$content,
        ':i'=>$image,
        ':u'=>$user
    ]);

    return $this->db->lastInsertId();
    }


    public function update($id,$title,$content,$image){

    $stmt = $this->db->prepare("
        UPDATE guides
        SET title = ?, content = ?, image = ?
        WHERE id = ?
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

    public function removeImage($id){

    $stmt = $this->db->prepare("
        UPDATE guides
        SET image = NULL
        WHERE id = ?
    ");

    return $stmt->execute([$id]);
    }

    public function search($q){

    $stmt = $this->db->prepare("
        SELECT DISTINCT g.*
        FROM guides g
        LEFT JOIN guide_categories gc ON g.id = gc.guide_id
        LEFT JOIN categories c ON gc.category_id = c.id
        WHERE
            g.title ILIKE ?
            OR g.content ILIKE ?
            OR c.name ILIKE ?
        ORDER BY g.created_at DESC
    ");

    $like = "%$q%";

    $stmt->execute([$like,$like,$like]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($catId){

    $stmt = $this->db->prepare("
        SELECT g.*
        FROM guides g
        JOIN guide_categories gc
        ON g.id = gc.guide_id
        WHERE gc.category_id = ?
        ORDER BY g.created_at DESC
    ");

    $stmt->execute([$catId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

}
