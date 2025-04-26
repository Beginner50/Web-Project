<?php
require '../vendor/autoload.php';

use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\SchemaLoader;
use Opis\JsonSchema\Validator;

class User
{
    protected $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAllUsers($userType = "all", $userID = 0, $limit = 25, $offset = 0)
    {
        // Base query parts
        $queries = [
            'student' => "
                SELECT 'Student' AS UserType, user.UserID, DateOfBirth, FirstName, LastName, Email, Gender, " . ($userID != 0 ? "Password," : "")  . "1 AS IsApproved
                FROM user 
                INNER JOIN student ON user.UserID = student.StudentID "
                . ($userID != 0 ? "WHERE user.UserID = " .  $userID : "") .
                " LIMIT ? OFFSET ?;",
            'teacher' => "
                SELECT 'Teacher' AS UserType, user.UserID, DateOfBirth, FirstName, LastName, Email, Gender, " . ($userID != 0 ? "Password," : "") . "approval.IsApproved 
                FROM user 
                INNER JOIN teacher ON user.UserID = teacher.TeacherID
                LEFT JOIN approval ON user.UserID = approval.UserID "
                . ($userID != 0 ? "WHERE user.UserID = " .  $userID : "") .
                " LIMIT ? OFFSET ?;
            ",
            'admin' => "
                SELECT 'Admin' AS UserType, user.UserID, DateOfBirth, FirstName, LastName, Email, Gender, " . ($userID != 0 ? "Password," : "") . "approval.IsApproved 
                FROM user 
                INNER JOIN administrator ON user.UserID = administrator.AdminID
                LEFT JOIN approval ON user.UserID = approval.UserID "
                . ($userID != 0 ? "WHERE user.UserID = " .  $userID : "") .
                " LIMIT ? OFFSET ?;
            "
        ];

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

        if (empty($users))
            return ["success" => 0, "errors" => ["Could not find user(s)!"]];

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
        $result = (int)$stmt->fetchColumn();
        $stmt->closeCursor();
        return $result;
    }

    protected function create($userData, $approval = false)
    {
        $userType = strtoupper($userData["user-type"][0]) . substr($userData["user-type"], 1, strlen($userData["user-type"]) - 1);
        try {
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT);
            $sInsertUser = $this->pdo->prepare('INSERT INTO user(DateOfBirth, FirstName, LastName,Email,Gender,Password) VALUES(?,?,?,?,?,?);');
            $sInsertUser->execute([$userData["dob"], $userData["fname"], $userData["lname"], $userData["email"], $userData["gender"], $passwordHash]);
            $sInsertUser->closeCursor();

            $sGetUserID = $this->pdo->query("SELECT LAST_INSERT_ID();");
            $sGetUserID->execute();
            $userID = $sGetUserID->fetchAll(PDO::FETCH_NUM)[0][0];

            if ($approval == true) {
                $sInsertApproval = $this->pdo->prepare('INSERT INTO approval(UserID, UserType) VALUES(?, ?);');
                $sInsertApproval->bindParam(1, $userID, PDO::PARAM_INT);
                $sInsertApproval->bindParam(2, $userType, PDO::PARAM_STR);
                $sInsertApproval->execute();
                $sInsertApproval->closeCursor();
            }

            return ["success" => 1, "data" => ["UserID" => $userID]];
        } catch (PDOException $e) {
            return ["success" => 0, "errors" => array($e->getMessage())];
        }
    }

    public function edit($userData, $approval = false)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE user SET 
                                         DateOfBirth = ?, FirstName = ?, LastName = ?,
                                         Email = ?, Gender = ?, Password = ?
                                         WHERE UserID = ?");
            $stmt->execute([
                $userData["dob"],
                $userData["fname"],
                $userData["lname"],
                $userData["email"],
                $userData["gender"],
                $userData["password"],
                $userData["userID"]
            ]);


            if ($approval) {
                $stmt = $this->pdo->prepare("UPDATE approval SET AdminID = ?, IsApproved = ? WHERE UserID = ?");
                $stmt->execute([
                    $userData["self-userID"],
                    $userData["is-approved"],
                    $userData["userID"]
                ]);
            }

            return ["success" => 1];
        } catch (PDOException $e) {
            return ["success" => 0, "errors" => [$e->getMessage()]];
        }
    }

    public function delete($userID)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM user WHERE UserID = ?");
            $stmt->execute([$userID]);
            $stmt->closeCursor();

            return ["success" => 1];
        } catch (PDOException $e) {
            return ["success" => 0, "errors" => array("Could not delete user!")];
        }
    }

    public function findUserID($email)
    {
        // UserID
        $stmt = $this->pdo->prepare("SELECT UserID FROM user WHERE Email = ? LIMIT 1");
        $stmt->execute([$email]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
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
        $stmt->closeCursor();

        return ["success" => 1, "data" => ["UserID" => $userID, "UserType" => $userType]];
    }

    protected function validateUser($schemaData, $userData)
    {
        if (isset($userData['dob'])) {
            $userData['dob'] = str_replace('/', '-', $userData['dob']);
        }

        // Validate the data against the schema
        $validator = new Validator();
        $result = $validator->validate((object) $userData, $schemaData);

        // Additional checks for user credentials
        $user = new User($this->pdo);
        if ($result->isValid()) {
            return ["success" => 1, "errors" => []];
        } else {
            return ["success" => 0, "errors" => [...array_values((new ErrorFormatter())->format($result->error()))][0]];
        }
    }
}
