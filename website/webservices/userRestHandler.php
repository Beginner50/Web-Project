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

    /*
        Provides just enough information for tabulation of users

        Optional URL Query Parameters (See .htaccess for usage):
        - userID - Single user selection (Shows Password for single user)
        - user-type - Selects only user of that type
        - limit - Limit selection
        - offset - offset selection
    */
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

    /*
        Authenticates and returns the userID & userType

        POST:
        {
            "email": "xyz@mail.com",
            "password": "tesmp"
        }

        Return:
            userID, userType
    */
    public function authenticateUser()
    {
        $result = ["success" => 1, "errors" => array()];

        $user = new User($this->pdo);
        $response = $user->findUserID($_POST["email"]);
        if (!$response["success"])
            $result["errors"][] = "Email does not exist!";
        else {
            $userID = $response["data"]["userID"];
            $userType = strtolower($response["data"]["userType"]);
            $userData = json_decode(file_get_contents("http://localhost/" . $userID), true)["data"][0];

            if (!password_verify($_POST["password"], $userData["Password"]))
                $result["errors"][] = "Invalid password!";
            if (!$userData["Authorisation"])
                $result["errors"][] = "User is not authorised!";
        }

        if (sizeof($result["errors"]) > 0)
            $result["success"] = 0;
        else
            $result["data"] = ["userID" => $userID, "userType" => $userType];

        $this->setHttpHeaders("application/json", $result["success"] == 1 ? 200 : 401);
        echo json_encode($result);
        exit;
    }

    /*
        Validate, add and returns the userID & userType

        POST:
        {
	        "user-type", "fname", "lname", "email", "gender", "dob", "password", "repeat-password"
	        "class-group", "level", "subjects"
	        "subject-taught", "teacher-date-joined"
	        "admin-date-joined"
        }

        Return:
            userID, userType
    */
    public function addUser()
    {
        // Route user-type
        switch ($_GET['user-type']) {
            case "student":
                $student = new Student($this->pdo);
                $result = $student->addStudent();
                break;
            case "teacher":
                $teacher = new Teacher($this->pdo);
                $result = $teacher->addTeacher();
                break;
            case "admin":
                $admin = new Admin($this->pdo);
                $result = $admin->addAdmin();
                break;
            default:
                break;
        }
        if ($result["success"]) {
            $response = json_decode(file_get_contents("http://localhost/users/" . $_GET['user-type'] . "/" . $result['data']), true);
            if ($response["success"])
                $result["data"] = $response["data"];
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
