<?php

require_once "simpleRest.php";
require_once "models/ClassMessage.php";
require_once "models/ClassStudent.php";
require_once "models/ClassTeacher.php";
require_once "models/Class.php";

class ClassRestHandler extends SimpleRest
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /*
    URL Query Arguments:
    classID      -  Single class selection
    limit -         Limits selection
    offset -        Offset
    */
    public function getAllClasses()
    {
        $classID = isset($_GET["classID"]) ? (int)$_GET["classID"] : 0;
        $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 10;
        $offset = isset($_GET["offset"]) ? (int)$_GET["offset"] : 0;

        $class = new Classroom($this->pdo);
        $rawData = $class->getAllClasses(classID: $classID, limit: $limit, offset: $offset);

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }

    /*
    URL Query Arguments:
    classID      -  Single class selection
    limit -         Limits selection
    offset -        Offset
    */
    public function getClassMessagesByClassIDs()
    {
        $classID = isset($_GET["classID"]) ? (int)$_GET["classID"] : 0;
        $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 10;
        $offset = isset($_GET["offset"]) ? (int)$_GET["offset"] : 0;

        $classMessage = new ClassMessage($this->pdo);
        $rawData = $classMessage->getAllMessagesByClassIDs(classID: $classID, limit: $limit, offset: $offset);

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }

    /*
    URL Query Arguments:
    userID      -  Single class selection
    */
    public function getClassesEnrolledByStudentIDs()
    {
        $userID = $_GET["userID"] ?? 0;

        $classStudent = new ClassStudent($this->pdo);
        $rawData = $classStudent->getAllClassesEnrolledByStudentIDs(userID: $userID);

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }

    /*
    URL Query Arguments:
    userID      -  Single class selection
    */
    public function getClassesTaughtByTeacherIDs()
    {
        $userID = $_GET["userID"] ?? 0;

        $classTeacher = new ClassTeacher($this->pdo);
        $rawData = $classTeacher->getAllClassesTaughtByTeacherIDs(userID: $userID);

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }

    /*
        Enrolls students based on their subjects taken and their level & class group.
        If no class is found, create the corresponding class

        POST:
        {
            "subjects": array
        }
    */
    public function enrollStudentInClasses()
    {
        $errors = [];
        $subjects = json_decode($_POST["subjects"]);

        // Get student data
        $response = json_decode(file_get_contents("http://localhost/users/student/" . $_GET["userID"]), true);
        if (!$response["success"]) return $response;
        $studentData = $response["data"][0];

        $class = new Classroom($this->pdo);
        $classStudent = new ClassStudent($this->pdo);
        foreach ($subjects as $subject) {
            // Find classID (Create class if not found)
            if (!($result = $class->findClassID($studentData["Level"], $studentData["ClassGroup"], $subject))["success"])
                $result = $class->create($studentData["Level"], $studentData["ClassGroup"], $subject);
            $classID = $result["data"]["classID"];

            // Enroll student in class
            $result = $classStudent->enrollStudentInClass($studentData["UserID"], $classID);
            if (!$result["success"])
                array_push($errors, ...$result["errors"]);
        }

        $statusCode = count($errors) > 0 ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        if (count($errors) > 0)
            echo json_encode(["success" => 0, "errors" => [...$errors]]);
        else
            echo json_encode(["success" => 1]);
        exit;
    }
}
