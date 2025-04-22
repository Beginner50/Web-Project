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
        $userID = $_GET["userID"] ?? 0;
        $limit = $_GET["limit"] ?? 25;
        $offset = $_GET["offset"] ?? 0;

        switch ($userType) {
            case "student":
                $student = new Student($this->pdo);
                $rawData = $student->getAllStudents(userID: $userID, limit: $limit, offset: $offset);
                break;
            case "teacher":
                $teacher = new Teacher($this->pdo);
                $rawData = $teacher->getAllTeachers(userID: $userID, limit: $limit, offset: $offset);
                break;
            case "admin":
                $admin = new Admin($this->pdo);
                $rawData = $admin->getAllAdmins(userID: $userID, limit: $limit, offset: $offset);
                break;
            default:
                $user = new User($this->pdo);
                $rawData = $user->getAllUsers(userID: $userID, limit: $limit, offset: $offset);
                break;
        }

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($rawData);
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
    public function createUser()
    {
        $result = ["success" => 1, "errors" => array()];

        // Clean & Validate POST request
        if (isset($_POST["admin-date-joined"])) {
            $_POST["date-joined"] = $_POST["admin-date-joined"];
            unset($_POST["admin-date-joined"]);
        } else if (isset($_POST["teacher-date-joined"])) {
            $_POST["date-joined"] = $_POST["teacher-date-joined"];
            unset($_POST["teacher-date-joined"]);
        }
        $_POST["subjects"] = json_decode($_POST["subjects"], true);

        $userData = $_POST;

        // Route user-type
        switch ($_GET['user-type']) {
            case "student":
                $student = new Student($this->pdo);
                if (!($result = $student->findUserID($userData["email"]))["success"])
                    break;
                else
                    $studentID = $result["data"]["userID"];

                // Enroll student in classes based on subjects taken
                $response = $this->sendPostRequest(
                    "http://localhost/classes/enroll/" . $studentID,
                    $userData['subjects']
                );

                if (!$response["success"]) {
                    $result["success"] = 0;
                    $result["errors"] = [...$result["errors"], ...$response["errors"]];
                }
                break;
            case "teacher":
                $teacher = new Teacher($this->pdo);
                $result = $teacher->create($userData);
                break;
            case "admin":
                $admin = new Admin($this->pdo);
                $result = $admin->create($userData);
                break;
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
        $userID = 0;
        $userType = "";

        $user = new User($this->pdo);
        $response = $user->findUserID($_POST["email"]);
        if (!$response["success"])
            $result["errors"][] = "Email does not exist!";
        else {
            $userID = $response["data"]["userID"];
            $userType = strtolower($response["data"]["userType"]);

            $user = new User($this->pdo);
            $userData = $user->getAllUsers(userID: $userID)["data"][0];

            if (!password_verify($_POST["password"], $userData["Password"]))
                $result["errors"][] = "Invalid password!";
            if (!$userData["IsApproved"])
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
}
