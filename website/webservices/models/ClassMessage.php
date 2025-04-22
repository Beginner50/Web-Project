<?php
class ClassMessage
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMessagesByClassIds($limit = 10, $offset = 0) {}

    public function create($userID, $message) {}
}
