<?php
require_once "models/Subject.php";
require_once "simpleRest.php";

class SubjectRestHandler extends SimpleRest
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllSubjects()
    {
        $subject = new Subject($this->pdo);
        $rawData = $subject->getAllSubjects();

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }
}
