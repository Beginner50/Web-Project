<?php
require_once "User.php";

class Teacher extends User
{
    public function getAllTeachers()
    {
        $result = $this->getAllUsers("teacher");
        $teachers = array_map(function ($u) {
            $userID = $u["UserID"];

            // Get teacher data per userID
            $stmt = $this->pdo->prepare("SELECT SubjectTaught, DateJoined from teacher WHERE TeacherID = ?;");
            $stmt->execute([$userID]);
            $teacherData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get teacher class taught
            $response = json_decode(file_get_contents("http://localhost/users/teacher/" . $userID . "/classes"), true);
            if (!$response["success"])
                return ["success" => 0, "errors" => array("Could not get classes taught!")];

            $classesTaughtByTeacherIDs = $response["data"][0];
            $classesTaught = $classesTaughtByTeacherIDs[$userID];
            return [...$u, ...$teacherData[0], "ClassesTaught" => $classesTaught];
        }, $result["data"]);

        $result["data"] = $teachers;
        return $result;
    }

    public function addTeacher()
    {
        $result = $this->validateTeacher();
        if (!$result["success"])
            return $result;

        $this->pdo->beginTransaction();
        try {
            $userData = $_GET["user"];
            $teacherID = $this->addUser($userData, true);

            $sInsertTeacher = $this->pdo->prepare('INSERT INTO teacher(TeacherID, SubjectTaught, DateJoined) VALUES(?, ?, ?);');
            $sInsertTeacher->execute([$teacherID, $userData["subjectTaught"], $userData["datejoined"]]);
            $sInsertTeacher->closeCursor();

            $this->pdo->commit();
            $result["data"]["userID"] = $teacherID;
        } catch (Exception $e) {
            var_dump($e);
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e;
        }
        return $result;
    }

    public function validateTeacher()
    {
        $userData = $_POST["user"];
        $result = $this->validateUser();

        if ($result["success"] == 1) {
            $subjectTaught = htmlspecialchars($userData["subjectTaught"] ?? '');
            $dateJoined = htmlspecialchars($userData["dateJoined"] ?? '');

            if (empty($subjectTaught)) {
                $result["errors"][] = "Subject taught cannot be blank!";
            }
            if (empty($dateJoined)) {
                $result["errors"][] = "Date joined cannot be empty!";
            } elseif (!strtotime($dateJoined)) {
                $result["errors"][] = "Invalid date format!";
            }
        }
        if (sizeof($result["errors"]) > 0)
            $result["success"] = 0;
        return $result;
    }
}
