<?php

require_once "simpleRest.php";
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
        $class = new Classroom($this->pdo);
        $rawData = $class->getAllClasses();

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }

    public function getClassesEnrolledByStudentIDs()
    {
        $classStudent = new ClassStudent($this->pdo);
        $rawData = $classStudent->getAllClassesEnrolledByStudentIDs();

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }

    public function getClassesTaughtByTeacherIDs()
    {
        $classTeacher = new ClassTeacher($this->pdo);
        $rawData = $classTeacher->getAllClassesTaughtByTeacherIDs();

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
