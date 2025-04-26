<?php
require_once "User.php";

class Teacher extends User
{
    public function getAllTeachers($userID = 0, $limit = 25, $offset = 0)
    {
        $result = $this->getAllUsers(userType: "teacher", userID: $userID, limit: $limit, offset: $offset);
        if (!$result["success"]) return $result;
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
            $teacherID = User::create($userData, true)["data"]["UserID"];

            $sInsertTeacher = $this->pdo->prepare('INSERT INTO teacher(TeacherID, SubjectTaught, DateJoined) VALUES(?, ?, ?);');
            $sInsertTeacher->execute([$teacherID, $userData["subject-taught"], $userData["date-joined"]]);
            $sInsertTeacher->closeCursor();

            $this->pdo->commit();
            $result["data"] = ["UserID" => $teacherID, "UserType" => $userData["user-type"]];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e;
        }
        return $result;
    }

    public function edit($userData, $approval = true)
    {
        if ($userData["self-userID"] != $userData["userID"] && $userData["self-user-type"] != "admin")
            return ["success" => 0, "errors" => "Not Authorised!"];

        try {
            $this->pdo->beginTransaction();
            User::edit($userData, (isset($userData["is-approved"]) && $userData["self-user-type"] == "admin"));

            $stmt = $this->pdo->prepare("UPDATE teacher SET DateJoined = ?, SubjectTaught = ?
                                        WHERE TeacherID = ?;");
            $stmt->bindParam(1, $userData["date-joined"]);
            $stmt->bindParam(2, $userData["subject-taught"]);
            $stmt->bindParam(3, $userData["userID"]);
            $stmt->execute();

            $this->pdo->commit();
            return ["success" => 1];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            return ["success" => 0, "errors" => [$e->getMessage()]];
        }
    }

    public function validateTeacher($userData)
    {
        if (isset($userData['date-joined'])) {
            $userData['date-joined'] = str_replace('/', '-', $userData['date-joined']);
        }
        $schemaData = json_decode(file_get_contents("schemas/teacherSchema.json"));
        return $this->validateUser($schemaData, $userData);
    }
}
