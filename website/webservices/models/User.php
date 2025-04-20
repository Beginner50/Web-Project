<?php

class User
{
    protected $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAllUsers()
    {
        // Set default pagination values
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $userID = isset($_GET['userID']) ? (int)$_GET['userID'] : 0;
        $userType = isset($_GET['user-type']) ? $_GET['user-type'] : "all";

        // Base query parts
        $queries = [
            'student' => "
                SELECT 'Student' AS UserType, user.UserID, DateOfBirth, FirstName, LastName, Email, Gender, " . ($userID != 0 ? "Password," : "")  . "1 AS Authorisation 
                FROM user 
                INNER JOIN student ON user.UserID = student.StudentID "
                . ($userID != 0 ? "WHERE user.UserID = " . $userID : "") .
                " LIMIT ? OFFSET ?;",
            'teacher' => "
                SELECT 'Teacher' AS UserType, user.UserID, DateOfBirth, FirstName, LastName, Email, Gender, " . ($userID != 0 ? "Password," : "") . "approval.IsApproved AS Authorisation 
                FROM user 
                INNER JOIN teacher ON user.UserID = teacher.TeacherID
                LEFT JOIN approval ON user.UserID = approval.UserID "
                . ($userID != 0 ? "WHERE user.UserID = " . $userID : "") .
                " LIMIT ? OFFSET ?;
            ",
            'admin' => "
                SELECT 'Admin' AS UserType, user.UserID, DateOfBirth, FirstName, LastName, Email, Gender, " . ($userID != 0 ? "Password," : "") . "approval.IsApproved AS Authorisation 
                FROM user 
                INNER JOIN administrator ON user.UserID = administrator.AdminID
                LEFT JOIN approval ON user.UserID = approval.UserID "
                . ($userID != 0 ? "WHERE user.UserID = " . $userID : "") .
                " LIMIT ? OFFSET ?;
            "
        ];

        try {
            $users = array();

            // Get users
            $queries = ($userType != "all" ?  [$queries[$userType]] : $queries);
            foreach ($queries as $query) {
                $stmt = $this->pdo->prepare($query);
                $stmt->bindValue(1, $limit, PDO::PARAM_INT);
                $stmt->bindValue(2, $offset, PDO::PARAM_INT);
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($res)) array_push($users, ...$res);
            }

            return [
                'success' => 1,
                'data' => $users,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($users),
                    'total' => ($userID == 0 ? $this->getTotalUsersCount($userType) : 1)
                ]
            ];
        } catch (PDOException $e) {
            return [
                'success' => 0,
                'errors' => array('Database error: ' . $e->getMessage())
            ];
        }
    }

    private function getTotalUsersCount($userType)
    {
        $sql = "";
        switch ($userType) {
            case 'student':
                $sql = "SELECT COUNT(*) FROM user INNER JOIN student ON user.UserID = student.StudentID";
                break;
            case 'teacher':
                $sql = "SELECT COUNT(*) FROM user INNER JOIN teacher ON user.UserID = teacher.TeacherID";
                break;
            case 'admin':
                $sql = "SELECT COUNT(*) FROM user INNER JOIN administrator ON user.UserID = administrator.AdminID";
                break;
            default:
                $sql = "SELECT COUNT(*) FROM user";
                break;
        }

        $stmt = $this->pdo->query($sql);
        return (int)$stmt->fetchColumn();
    }

    protected function addUser($userData, $approval)
    {
        try {
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT);
            $sInsertUser = $this->pdo->prepare('INSERT INTO user(DateOfBirth, FirstName, LastName,Email,Gender,Password) VALUES(?,?,?,?,?,?);');
            $sInsertUser->execute([$userData["dob"], $userData["fname"], $userData["lname"], $userData["email"], $userData["gender"], $passwordHash]);
            $sInsertUser->closeCursor();

            $sGetUserID = $this->pdo->query("SELECT LAST_INSERT_ID();");
            $sGetUserID->execute();
            $userID = $sGetUserID->fetchAll(PDO::FETCH_NUM)[0][0];

            if ($approval == true) {
                $sInsertApproval = $this->pdo->prepare('INSERT INTO approval(AdminID, UserID, UserType, IsApproved) VALUES(null, ?, ?, ?);');
                $sInsertApproval->execute([$userID, $userData["user-type"], 0]);
                $sInsertApproval->closeCursor();
            }
            return ["success" => 1, "data" => $userID];
        } catch (PDOException $e) {
            return ["success" => 0, "errors" => array($e->getMessage())];
        }
    }

    public function validateUser()
    {
        $userData = $_POST ?? null;
        $result = ["success" => 0, "errors" => []];

        // Early return if empty user data
        if (empty($userData)) {
            $result["errors"][] = "Empty/Invalid Form submission!";
            return $result;
        }

        // Validate user type
        $userType = htmlspecialchars($userData["user-type"] ?? '');
        if (empty($userType)) {
            $result["errors"][] = "User Type is required!";
        }

        // Validate general attributes
        $firstName = htmlspecialchars($userData["fname"] ?? '');
        if (empty($firstName)) {
            $result["errors"][] = "First Name is required!";
        } elseif (!preg_match('/^[a-zA-Z \-]+$/', $firstName)) {
            $result["errors"][] = "First Name contains invalid characters!";
        }

        $lastName = htmlspecialchars($userData["lname"] ?? '');
        if (empty($lastName)) {
            $result["errors"][] = "Last Name is required!";
        } elseif (!preg_match('/^[a-zA-Z \-]+$/', $lastName)) {
            $result["errors"][] = "Last Name contains invalid characters!";
        }

        // Email validation
        $email = htmlspecialchars($userData["email"] ?? '');
        if (empty($email)) {
            $result["errors"][] = "Email is required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result["errors"][] = "Invalid email format!";
        } elseif ($this->findUserID($email)["success"]) {
            $result["errors"][] = "User already exists with this email!";
        }

        $gender = htmlspecialchars($userData["gender"] ?? '');
        if (empty($gender)) {
            $result["errors"][] = "Gender is required!";
        } elseif (!in_array($gender, ['M', 'F', 'Other'])) {
            $result["errors"][] = "Invalid gender selection!";
        }

        $dateOfBirth = htmlspecialchars($userData["dob"] ?? '');
        if (empty($dateOfBirth)) {
            $result["errors"][] = "Date of Birth is required!";
        } elseif (!strtotime($dateOfBirth)) {
            $result["errors"][] = "Invalid Date of Birth format!";
        } elseif (strtotime($dateOfBirth) > strtotime('-13 years')) {
            $result["errors"][] = "You must be at least 13 years old!";
        }

        // Password validation
        $password = $userData["password"] ?? '';
        $repeatPassword = $userData["repeat-password"] ?? '';

        if (empty($password)) {
            $result["errors"][] = "Password is required!";
        } else {
            if (strlen($password) < 5) {
                $result["errors"][] = "Password must be at least 5 characters!";
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $result["errors"][] = "Password must contain at least 1 uppercase letter!";
            }
            if (!preg_match('/[a-z]/', $password)) {
                $result["errors"][] = "Password must contain at least 1 lowercase letter!";
            }
            if (!preg_match('/[0-9]/', $password)) {
                $result["errors"][] = "Password must contain at least 1 number!";
            }
            // if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            //     $result["errors"][] = "Password must contain at least 1 special character!";
            // }
        }

        if (empty($repeatPassword)) {
            $result["errors"][] = "Please repeat your password!";
        } elseif ($password !== $repeatPassword) {
            $result["errors"][] = "Passwords do not match!";
        }

        // Mark as successful if no errors
        if (empty($result["errors"])) {
            $result["success"] = 1;
        }

        return $result;
    }

    public function findUserID($email)
    {
        try {
            // UserID
            $stmt = $this->pdo->prepare("SELECT UserID FROM user WHERE Email = ? LIMIT 1");
            $stmt->execute([$email]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result == NULL)
                return ["success" => 0, "errors" => array("Could not find user!")];
            else
                $userID = $result[0]["UserID"];

            // UserType
            $stmt = $this->pdo->prepare("(SELECT 'Student' AS UserType FROM student WHERE StudentID = ?)
                                         UNION
                                         (SELECT 'Teacher' AS UserType FROM teacher WHERE TeacherID = ?)
                                         UNION
                                         (SELECT 'Admin' AS UserType FROM administrator WHERE AdminID = ?);");
            $stmt->execute([$userID, $userID, $userID]);
            $userType = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["UserType"];

            return ["success" => 1, "data" => ["userID" => $userID, "userType" => $userType]];
        } catch (PDOException $e) {
            return ["success" => 0, "errors" => $e->getMessage()];
        }
    }
}
