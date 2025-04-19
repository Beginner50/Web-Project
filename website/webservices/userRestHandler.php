<?php
require_once "simpleRest.php";
require_once "models/User.php";
require_once "models/Student.php";
require_once "models/Teacher.php";
require_once "models/Admin.php";

class UserRestHandler extends SimpleRest
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllUsers()
    {
        $rawData = null;
        $userType = $_GET["user-type"] ?? "all";
        switch ($userType) {
            case "student":
                $student = new Student($this->pdo);
                $rawData = $student->getAllStudents();
                break;
            case "teacher":
                $teacher = new Teacher($this->pdo);
                $rawData = $teacher->getAllTeachers();
                break;
            case "admin":
                $admin = new Admin($this->pdo);
                $rawData = $admin->getAllAdmins();
                break;
            default:
                $user = new User($this->pdo);
                $rawData = $user->getAllUsers();
                break;
        }

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
        exit;
    }

    public function authenticateUser()
    {
        $result = ["success" => 1, "errors" => array()];

        $user = new User($this->pdo);
        if (!($_GET["userID"] = $user->findUserID())) {
            $result["errors"][] = "Email does not exist!";
        } else {
            $userData = $this->getAllUsers();

            if (!password_verify($_POST["password"], $userData["password"]))
                $result["errors"][] = "Invalid password!";
        }

        if (sizeof($result["errors"]) > 0)
            $result["success"] = 0;

        $this->setHttpHeaders("application/json", $result["success"] == 1 ? 200 : 401);
        echo json_encode($result);
        exit;
    }

    public function addUser()
    {
        switch ($_GET["user-type"]) {
            case "student":
                $student = new Student($this->pdo);
                $result = $student->addStudent();
            case "teacher":
                $teacher = new Teacher($this->pdo);
                $result = $teacher->addTeacher();
            case "admin":
                $admin = new Admin($this->pdo);
                $result = $admin->addAdmin();
            default:
                break;
        }

        $statusCode = $result["success"] == 1 ? 201 : 400;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($result);
        exit;
    }

    public function editUser()
    {
        // Write your code here
    }
}
