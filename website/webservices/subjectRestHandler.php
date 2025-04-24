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

    For user-type & userID agnostic selection only:
        limit           Limit selection
        offset          Offset selection
    */
    public function getAllSubjects()
    {
        $userType = $_GET["user-type"] ?? "all";
        $userID = $_GET["userID"] ?? 0;
        $limit = isset($_GET["limit"]) ? (int) ($_GET["limit"]) : 20;
        $offset = isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

        $subject = new Subject($this->pdo);
        if ($userType == "all")
            $rawData = $subject->getAllSubjects(limit: $limit, offset: $offset);
        else if ($userType == "student")
            $rawData = $subject->getSubjectsByStudentIDs(userID: $userID);

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }
}
