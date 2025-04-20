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

    public function enrollStudentInClasses()
    {
        // Get & filter list of classes
        $class = new Classroom($this->pdo);
        $classes = $class->getAllClasses()["data"];
    }
}
