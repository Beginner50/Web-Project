<?php

class Subject
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllSubjects()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM subject;");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $subjects;
    }
}
