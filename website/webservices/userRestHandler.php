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
        $limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 25;
        $offset =  isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

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
        Validate, add user and returns the userID & userType

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

        // Clean POST data
        $this->preprocessPOSTData("create");
        $userData = $_POST;

        // Route user-type
        switch ($userData['user-type']) {
            case "student":
                $student = new Student($this->pdo);
                if (($result = $student->create($userData))["success"]) {
                    // Enroll student in classes based on subjects taken if student has been created successfully
                    $studentID = $result["data"]["UserID"];
                    $response = $this->sendPostRequest(
                        "http://localhost/classes/enroll/" . $studentID,
                        $userData['subjects']
                    );
                    $result["success"] = $response["success"];
                    $result["errors"] = [...$response["errors"]];
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

    /*
        Validate & edit user 

        URL Arguments:
        reset-password  - Resets user password if true

        POST:
        { 
            "user-type", "userID", 
            "self-userID", "self-user-type"
        }
    */
    public function editUser()
    {
        $result = ["success" => 1, "errors" => array()];
 
        $this->preprocessPOSTData("edit"); 
        $userData = $_POST;
    
       
        $user = new User($this->pdo);
        $result = $user->edit($userData);  
    
        if ($result["success"] == 1) {
           
            switch ($_POST['user-type']) {
                case "student":
                    $student = new Student($this->pdo);
                    $result = $student->edit($userData);  
    
                    // 🔁 Unenroll and re-enroll
                    $response = json_decode(
                        file_get_contents("http://localhost/classes/unenroll/" . $userData["userID"]),
                        true
                    );
                    if (!$response["success"]) {
                        $result["success"] = 0;
                        $result["errors"] = [...$result["errors"], ...$response["errors"]];
                        break;
                    }
    
                    $response = $this->sendPostRequest(
                        "http://localhost/classes/enroll/" . $userData["userID"],
                        $userData['subjects']
                    );
                    if (!$response["success"]) {
                        $result["success"] = 0;
                        $result["errors"] = [...$result["errors"], ...$response["errors"]];
                    }
                    break;
    
                case "teacher":
                    $teacher = new Teacher($this->pdo);
                    $result = $teacher->edit($userData); // updates subject-taught, date-joined, etc.
                    break;
    
                case "admin":
                    $admin = new Admin($this->pdo);
                    $result = $admin->edit($userData); // updates date-joined or admin-specific stuff
                    break;
            }
        }
     
        $statusCode = $result["success"] == 1 ? 201 : 400;
        return $result;

    }
    

    /*
        Delete User

        URL Arguments:
        userID
    */
    public function deleteUser()
    {
        $result = ["success" => 1, "errors" => array()];
        $userID = $_GET["userID"];

        $user = new User($this->pdo);
        $result = $user->delete($userID);

        $statusCode = $result["success"] == 1 ? 201 : 400;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($result);
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
        $userID = 0;
        $userType = "";

        $user = new User($this->pdo);
        $response = $user->findUserID($_POST["email"]);
        if (!$response["success"])
            $result["errors"][] = "Email does not exist!";
        else {
            $userID = $response["data"]["UserID"];
            $userType = strtolower($response["data"]["UserType"]);

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
            $result["data"] = ["UserID" => $userID, "UserType" => $userType];

        $this->setHttpHeaders("application/json", $result["success"] == 1 ? 200 : 401);
        echo json_encode($result);
        exit;
    }
    // approves a user (admin functionality)
    public function verifyUser()
    {
        $result = ["success" => 1, "errors" => array()];

        $adminID = $_POST['adminID'] ?? null;
        $targetUserID = $_POST['userID'] ?? null;

        if (!$adminID || !$targetUserID) {
            $result["success"] = 0;
            $result["errors"][] = "Missing adminID or userID!";
        } else {
            $user = new User($this->pdo);
            $result = $user->verifyUser($adminID, $targetUserID);
        }

        $this->setHttpHeaders("application/json", $result["success"] == 1 ? 200 : 400);
        echo json_encode($result);
        exit;
    }

    // reset password of a user
    public function resetPassword()
    {
        $result = ["success" => 1, "errors" => array()];

        $userID = $_POST['userID'] ?? null;

        if (!$userID) {
            $result["success"] = 0;
            $result["errors"][] = "Missing userID!";
        } else {
            $user = new User($this->pdo);
            $result = $user->resetPassword($userID);  // Calls your new model function
        }

        $this->setHttpHeaders("application/json", $result["success"] == 1 ? 200 : 400);
        echo json_encode($result);
        exit;
    }

    /*
        Clean & Validate POST Data
    */
    private function preprocessPOSTData($action)
    {
        if ($action == "create") {
            if (isset($_POST["admin-date-joined"])) {
                $_POST["date-joined"] = $_POST["admin-date-joined"];
                unset($_POST["admin-date-joined"]);
            } else if (isset($_POST["teacher-date-joined"])) {
                $_POST["date-joined"] = $_POST["teacher-date-joined"];
                unset($_POST["teacher-date-joined"]);
            }
            if (isset($_POST["subject-group"])) $_POST["subject-group"] = strtoupper($_POST["subject-group"]);

            $_POST["subjects"] = json_decode($_POST["subjects"], true);
        } else if ($action == "edit") {
            $resetPassword = $_GET["reset-password"] ?? false;
            $userID = $_POST["userID"];
            $userType = $_POST["user-type"];

            $userData = json_decode(
                file_get_contents("http://localhost/users/" . $userType . "/" . $userID),
                true
            )["data"][0];

          // Support both "firstname" and "fname"
            $_POST["fname"] = $_POST["fname"] ?? $_POST["firstname"] ?? $userData["FirstName"];
            $_POST["lname"] = $_POST["lname"] ?? $_POST["lastname"] ?? $userData["LastName"];
            $_POST["email"] = $_POST["email"] ?? $userData["Email"];
            $_POST["gender"] = $_POST["gender"] ?? $userData["Gender"];
            $_POST["dob"] = $_POST["dob"] ?? $userData["DateOfBirth"];
            if (isset($_POST["password"])) $_POST["password"] = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $_POST["password"] = $_POST["password"] ?? ($resetPassword ? "" : $userData["Password"]);

            if ($userType == "student") {
                $_POST["class-group"] = $_POST["class-group"] ?? $userData["ClassGroup"];
                $_POST["level"] = $_POST["level"] ?? $userData["Level"];
                if (isset($_POST["subjects"]))
                    $_POST["subjects"] = json_decode($_POST["subjects"], true);
                else
                    $_POST["subjects"] = array_map(function ($subject) {
                        return $subject["SubjectCode"];
                    }, $userData["Subjects"]);
            } else if ($userType == "teacher") {
                $_POST["subject-taught"] = $_POST["subject-taught"] ?? $userData["SubjectTaught"];
                $_POST["date-joined"] = $_POST["date-joined"] ?? $userData["DateJoined"];
            } else if ($userType == "admin") {
                $_POST["date-joined"] = $_POST["date-joined"] ?? $userData["DateJoined"];
            }
        }
    }

    public function deleteStudentSubject()
    {
        $result = ["success" => 1, "errors" => array()];
        
        $subjectCode = $_POST['subjectCode'] ?? null;
        $studentID = $_POST['userID'] ?? null;

        if (!$subjectCode || !$studentID) {
            $result["success"] = 0;
            $result["errors"][] = "Missing subjectCode or userID!";
        } else {
            $student = new Student($this->pdo);
            $result = $student->deleteSubject($studentID, $subjectCode);
        }

        $this->setHttpHeaders("application/json", $result["success"] == 1 ? 200 : 400);
        echo json_encode($result);
        exit;
    }


}
