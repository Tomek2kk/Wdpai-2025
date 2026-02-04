<?php

require_once __DIR__ . '/../../config/database.php';

class Comment {

    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByGuide($guideId){

        $stmt = $this->db->prepare("
            SELECT c.*, u.username
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.guide_id = ?
            ORDER BY c.created_at DESC
        ");

        $stmt->execute([$guideId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($guideId,$userId,$content){

        $stmt = $this->db->prepare("
            INSERT INTO comments
            (guide_id, user_id, content, created_at)
            VALUES (:guide, :user, :content, NOW())
        ");

        $stmt->execute([

            ':guide'   => (int)$guideId,
            ':user'    => (int)$userId,
            ':content' => $content
        ]);
    }

    public function delete($id){

        $stmt = $this->db->prepare("
            DELETE FROM comments WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }

    public function isOwner($commentId,$userId){

    $stmt = $this->db->prepare("
        SELECT id FROM comments
        WHERE id=? AND user_id=?
    ");

    $stmt->execute([$commentId,$userId]);

    return $stmt->fetch() !== false;
    }

    public function getById($id){

    $stmt = $this->db->prepare("
        SELECT *
        FROM comments
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => (int)$id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
