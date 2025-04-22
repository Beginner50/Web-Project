<?php
require_once "User.php";

class Teacher extends User
{
    public function getAllTeachers($userID = 0, $limit = 25, $offset = 0)
    {
        $result = $this->getAllUsers(userType: "teacher", userID: $userID, limit: $limit, offset: $offset);
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

            $classesTaught = $response["data"];
            return [...$u, ...$teacherData[0], "ClassesTaught" => $classesTaught];
        }, $result["data"]);

        $result["data"] = $teachers;
        return $result;
    }

    public function create($userData, $approval = true)
    {
        $result = $this->validateTeacher($userData);
        if (!$result["success"])
            return $result;

        try {
            $this->pdo->beginTransaction();
            $teacherID = User::create($userData, $approval)["data"]["userID"];

            $sInsertTeacher = $this->pdo->prepare('INSERT INTO teacher(TeacherID, SubjectTaught, DateJoined) VALUES(?, ?, ?);');
            $sInsertTeacher->execute([$teacherID, $userData["subject-taught"], $userData["date-joined"]]);
            $sInsertTeacher->closeCursor();

            $this->pdo->commit();
            $result["data"] = ["userID" => $teacherID, "userType" => $userData["user-type"]];
        } catch (Exception $e) {
            var_dump($e);
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e;
        }
        return $result;
    }

    public function validateTeacher($userData)
    {
        $result = User::validateUser($userData);

        if ($result["success"] == 1) {
            $subjectTaught = htmlspecialchars($userData["subject-taught"] ?? '');
            $dateJoined = htmlspecialchars($userData["date-joined"] ?? '');

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
