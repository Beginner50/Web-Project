<?php
require_once "User.php";

class Student extends User
{

    public function getAllStudents($userID = 0, $limit = 50, $offset = 0)
    {
        $result = $this->getAllUsers(userType: "student", userID: $userID, limit: $limit, offset: $offset);
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
        $result = $this->validateStudent($userData, false);
        if (!$result["success"])
            return $result;

        // Create student if not found
        try {
            $this->pdo->beginTransaction();

            $studentID = User::create($userData, false)["data"]["userID"];

            // Insert into student table
            $sInsertStudent = $this->pdo->prepare('INSERT INTO student(StudentID, Level, ClassGroup) VALUES(?, ?, ?);');
            $sInsertStudent->execute([$studentID, $userData["level"], $userData["class-group"]]);
            $sInsertStudent->closeCursor();

            $this->pdo->commit();
            $result["data"] = ["userID" => $studentID, "userType" => $userData["user-type"]];
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
        if (!($result = $this->validateStudent($userData, "edit"))["success"])
            return $result;

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

    public function validateStudent($userData, $action = "create")
    {
        $subjects = $userData["subjects"];
        $result = User::validateUser($userData, $action);

        if ($result["success"] == 1) {
            $classGroup = htmlspecialchars(strtoupper($userData["class-group"] ?? ''));
            $level = htmlspecialchars($userData["level"] ?? '');
            if ($subjects == NULL || empty($subjects))
                $result["errors"][] = "No subjects selected!";

            if (empty($classGroup)) {
                $result["errors"][] = "Class group cannot be blank!";
            }
            if (empty($level)) {
                $result["errors"][] = "Level cannot be blank!";
            } elseif (!filter_var($level, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                $result["errors"][] = "Level must be a positive integer!";
            }
            if (count($subjects) < 5) {
                $result["errors"][] = "You must select at least 5 subjects!";
            } else {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM subject WHERE SubjectCode IN (?, ?, ?, ?, ?)");
                $stmt->execute([...$subjects]);
                $count = $stmt->fetchAll(PDO::FETCH_NUM)[0][0];

                if ($count != count($subjects))
                    $result["errors"][] = "Invalid subjects selected!";
            }
        }
        if (count($result["errors"]) > 0)
            $result["success"] = 0;
        return $result;
    }
}
