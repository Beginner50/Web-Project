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

    /*
    URL Query Params:
    user-type       Lists subjects by user type
    userID          Single selection
    */
    public function getAllSubjects()
    {
        $userType = $_GET["user-type"] ?? "all";
        $userID = $_GET["userID"] ?? 0;

        $subject = new Subject($this->pdo);
        if ($userType == "all")
            $rawData = $subject->getAllSubjects();
        else if ($userType == "student")
            $rawData = $subject->getSubjectsByStudentIDs(userID: $userID);

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }
}
