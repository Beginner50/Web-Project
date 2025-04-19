<?php
require_once "User.php";

class Student extends User
{
    /*
        URL Query Arguments:

        userID - Single user selection (Shows Password for single user)
        limit - Limit selection
        offset - offset selection
    */
    public function getAllStudents()
    {
        $result = $this->getAllUsers();
        $students = array_map(function ($u) {
            $userID = $u["UserID"];

            // Get student data per userID
            $stmt = $this->pdo->prepare("SELECT Level, ClassGroup FROM student WHERE StudentID = ?");
            $stmt->execute([$userID]);
            $studentData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get student class enrolled
            $response = json_decode(file_get_contents("http://localhost/users/student/" . $userID . "/classes"), true);
            if (!$response["success"])
                return ["success" => 0, "errors" => array("Could not get classes enrolled!")];

            $classesEnrolledByStudentIDs = $response["data"][0];
            $classesEnrolled = $classesEnrolledByStudentIDs[$userID];
            return [...$u, ...$studentData[0], "ClassesEnrolled" => $classesEnrolled];
        }, $result["data"]);

        $result["data"] = $students;
        return $result;
    }

    public function addStudent()
    {
        $userData = $_POST["user"];
        $result = $this->validateStudent();
        if (!$result["success"])
            return $result;

        $this->pdo->beginTransaction();
        try {
            $studentID = $this->addUser($userData, false);

            // Insert into student table
            $sInsertStudent = $this->pdo->prepare('INSERT INTO student(StudentID, Level, ClassGroup) VALUES(LAST_INSERT_ID(),?,?);');
            $sInsertStudent->execute([$studentID, $userData["classGroup"], $userData["level"]]);
            $sInsertStudent->closeCursor();

            $sGetClassID = $this->pdo->prepare('SELECT ClassID FROM class WHERE SubjectCode = ? AND Level = ? AND ClassGroup = ?;');
            $sAssignClass = $this->pdo->prepare('INSERT INTO class_student(ClassID, StudentID) VALUES(?, ?);');

            foreach ($userData["subjects"] as $subject) {
                $sGetClassID->execute([$subject, $userData["level"], $userData["classGroup"]]);
                $classID = $sGetClassID->fetchAll(PDO::FETCH_NUM)[0];
                $sGetClassID->closeCursor();
                $sAssignClass->execute([$classID[0], $studentID]);
                $sAssignClass->closeCursor();
            }

            $this->pdo->commit();
            $result["data"]["userID"] = $studentID;
        } catch (Exception $e) {
            var_dump($e);
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e;
        }
        return $result;
    }

    public function validateStudent()
    {
        $userData = $_POST["user"];
        $result = $this->validateUser();

        if ($result["success"] == 1) {
            $classGroup = htmlspecialchars(strtoupper($userData["classGroup"] ?? ''));
            $level = htmlspecialchars($userData["level"] ?? '');
            $subjects = $userData["subjects"] ?? [];

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
            }
        }
        if (sizeof($result["errors"]) > 0)
            $result["success"] = 0;
        return $result;
    }
}
