<?php
require_once "User.php";

class Student extends User
{

    public function getAllStudents($userID = 0, $limit = 50, $offset = 0)
    {
        $result = $this->getAllUsers(userType: "student", userID: $userID, limit: $limit, offset: $offset);
        if (!$result["success"]) return $result;
        $students = array_map(function ($u) {
            $userID = $u["UserID"];

            // Get student data per userID
            $stmt = $this->pdo->prepare("SELECT Level, ClassGroup FROM student WHERE StudentID = ?");
            $stmt->execute([$userID]);
            $studentData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get student subjects
            $response = json_decode(file_get_contents("http://localhost/users/student/" . $userID . "/subjects"), true);
            if (!$response["success"])
                return ["success" => 0, "errors" => array("Could not get subjects!")];

            $subjects = $response["data"];
            return [...$u, ...$studentData[0], "Subjects" => $subjects];
        }, $result["data"]);

        $result["data"] = $students;
        return $result;
    }

    public function create($userData, $approval = false)
    {
        $result = ["success" => 1, "errors" => array()];
        try {
            $this->pdo->beginTransaction();

            $studentID = User::create($userData, false)["data"]["UserID"];

            // Insert into student table
            $sInsertStudent = $this->pdo->prepare('INSERT INTO student(StudentID, Level, ClassGroup) VALUES(?, ?, ?);');
            $sInsertStudent->execute([$studentID, $userData["level"], $userData["class-group"]]);
            $sInsertStudent->closeCursor();

            $this->pdo->commit();
            $result["data"] = ["UserID" => $studentID, "UserType" => $userData["user-type"]];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e->getMessage();
            return $result;
        }

        return $result;
    }

    public function edit($userData, $approval = false)
    {
        if ($userData["self-userID"] != $userData["userID"] && $userData["self-user-type"] != "admin")
            return ["success" => 0, "errors" => "Not Authorised!"];

        try {
            $this->pdo->beginTransaction();
            User::edit($userData, approval: false);

            $stmt = $this->pdo->prepare("UPDATE student SET Level = ?, ClassGroup = ?
                                        WHERE StudentID = ?;");
            $stmt->bindParam(1, $userData["level"]);
            $stmt->bindParam(2, $userData["class-group"]);
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

    public function validateStudent(&$userData)
    {
        if (isset($userData["subjects"]) && is_string($userData["subjects"]))
            $userData["subjects"] = json_decode($userData["subjects"], true);

        $schemaData = json_decode(file_get_contents("schemas/studentSchema.json"));
        $subjects = array_values(array_map(
            function ($subject) {
                return $subject["SubjectCode"];
            },
            json_decode(file_get_contents("http://localhost/subjects"), true)["data"],
        ));
        $schemaData->properties->subjects->items->enum = $subjects;
        return $this->validateUser($schemaData, $userData);
    }
}
