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

        // Verify existence of user & password verfication
        $user = new User($this->pdo);
        if ($user->findUserID($_POST["email"])["success"]) {
            $result = ["success" => 0, "errors" => ["Email already exists!"]];
        }
        if ($_POST["password"] !== $_POST["repeat-password"]) {
            $result = ["success" => 0, "errors" => ["Passwords do not match!"]];
        }
        if (!$result["success"]) {
            $this->setHttpHeaders("application/json", 400);
            echo json_encode($result);
            exit;
        }

        // Route user-type
        switch ($_POST['user-type']) {
            case "student":
                $student = new Student($this->pdo);

                // Validate Student
                if (!($result = $student->validateStudent($_POST))["success"])
                    break;

                // Create student & enroll in classes if successful 
                if (($result = $student->create($_POST))["success"]) {
                    $studentID = $result["data"]["UserID"];
                    $response = $this->sendPostRequest(
                        "http://localhost/classes/enroll/" . $studentID,
                        $_POST['subjects']
                    );
                    $result["success"] = $response["success"];
                    $result["errors"] = [...$response["errors"]];
                }
                break;
            case "teacher":
                $teacher = new Teacher($this->pdo);

                // Validate Teacher
                if (!($result = $teacher->validateTeacher($_POST))["success"])
                    break;

                $result = $teacher->create($_POST);
                break;
            case "admin":
                $admin = new Admin($this->pdo);

                // Validate Admin
                if (!($result = $admin->validateAdmin($_POST))["success"])
                    break;

                $result = $admin->create($_POST);
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

        // Validate Edit & new Email
        $result = $this->validateEdit($_POST);
        if (isset($_POST["email"])) {
            $user = new User($this->pdo);
            $userID = $user->findUserID($_POST["email"])["data"]["UserID"];
            if ($userID != $_POST["userID"])
                $result["errors"][] = "Email already exists!";
        }

        if ($result["success"]) {
            switch ($_POST['user-type']) {
                case "student":
                    $student = new Student($this->pdo);

                    // Validate student
                    if (!($result = $student->getAllStudents(userID: $_POST['userID']))["success"])
                        break;

                    $this->formatUserData($_POST, $result["data"][0]);
                    if (!($result = $student->validateStudent($_POST))["success"])
                        break;

                    // Edit student
                    $result = $student->edit($_POST);

                    // Unenroll and re-enroll in classes
                    $response = json_decode(
                        file_get_contents("http://localhost/classes/unenroll/" . $_POST["userID"]),
                        true
                    );
                    if (!$response["success"]) {
                        $result["errors"] = [...$result["errors"], ...$response["errors"]];
                        break;
                    }
                    $response = $this->sendPostRequest(
                        "http://localhost/classes/enroll/" . $_POST["userID"],
                        $_POST['subjects']
                    );
                    if (!$response["success"])
                        $result["errors"] = [...$result["errors"], ...$response["errors"]];

                    break;
                case "teacher":
                    $teacher = new Teacher($this->pdo);

                    // Validate teacher
                    if (!($result = $teacher->getAllTeachers(userID: $_POST['userID']))["success"])
                        break;

                    $this->formatUserData($_POST, $result["data"][0]);
                    if (!($result = $teacher->validateTeacher($_POST))["success"])
                        break;

                    // Edit teacher
                    $result = $teacher->edit($_POST);
                    break;
                case "admin":
                    $admin = new Admin($this->pdo);

                    // Validate admin
                    if (!($result = $admin->getAllAdmins(userID: $_POST['userID']))["success"])
                        break;

                    $this->formatUserData($_POST, $result["data"][0]);
                    if (!($result = $admin->validateAdmin($_POST))["success"])
                        break;

                    // Edit admin
                    $result = $admin->edit($_POST);
                    break;
            }
        }

        if (isset($result["errors"]) && is_array($result["errors"]) && count($result["errors"]) > 0)
            $result["success"] = 0;
        $statusCode = $result["success"] == 1 ? 201 : 400;
        $this->setHttpHeaders("application/json", $statusCode);
        echo json_encode($result);
        exit;
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
            $userType = $response["data"]["UserType"];

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

    private function formatUserData(&$userData, $userDataOld)
    {
        $resetPassword = $_GET["reset-password"] ?? false;
        $userID = $userData["userID"];
        $userType = $userData["user-type"];

        // Support both "firstname" and "fname"
        $userData["fname"] = $userData["fname"] ?? $userData["firstname"] ?? $userDataOld["FirstName"];
        $userData["lname"] = $userData["lname"] ?? $userData["lastname"] ?? $userDataOld["LastName"];
        $userData["email"] = $userData["email"] ?? $userDataOld["Email"];
        $userData["gender"] = $userData["gender"] ?? $userDataOld["Gender"];
        $userData["dob"] = $userData["dob"] ?? $userDataOld["DateOfBirth"];
        if (isset($userData["password"])) $userData["password"] = password_hash($userData["password"], PASSWORD_BCRYPT);
        $userData["password"] = $userData["password"] ?? ($resetPassword ? "" : $userDataOld["Password"]);

        if ($userType == "student") {
            $userData["class-group"] = $userData["class-group"] ?? $userDataOld["ClassGroup"];
            $userData["level"] = $userData["level"] ?? (string) $userDataOld["Level"];
            if (isset($userData["subjects"]))
                $userData["subjects"] = json_decode($userData["subjects"], true);
            else
                $userData["subjects"] = array_map(function ($subject) {
                    return $subject["SubjectCode"];
                }, $userDataOld["Subjects"]);
        } else if ($userType == "teacher") {
            $userData["subject-taught"] = $userData["subject-taught"] ?? $userDataOld["SubjectTaught"];
            $userData["date-joined"] = $userData["date-joined"] ?? $userDataOld["DateJoined"];
        } else if ($userType == "admin") {
            $userData["date-joined"] = $userData["date-joined"] ?? $userDataOld["DateJoined"];
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
