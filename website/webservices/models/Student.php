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
        $result = $this->validateStudent();
        if (!$result["success"])
            return $result;
        $userData = $_POST;

        $this->pdo->beginTransaction();
        try {
            $response = $this->addUser($userData, false);
            if (!$response["success"]) throw new Exception(implode(", ", $response["errors"]));
            $studentID = $response["data"];

            // Insert into student table
            $sInsertStudent = $this->pdo->prepare('INSERT INTO student(StudentID, Level, ClassGroup) VALUES(?, ?, ?);');
            $sInsertStudent->execute([$studentID, $userData["level"], $userData["class-group"]]);
            $sInsertStudent->closeCursor();

            $sGetClassID = $this->pdo->prepare('SELECT ClassID FROM class WHERE SubjectCode = ? AND Level = ? AND ClassGroup = ?;');
            $sAssignClass = $this->pdo->prepare('INSERT INTO class_student(ClassID, StudentID) VALUES(?, ?);');

            foreach ($userData["subjects"] as $subject) {
                // Before adding a student to a class, find the list of available classes (Create classes if required)
                // If possible, create its own rest handler to assign classes to students
                // Implode $subjects, pass subjects, class-groups, level as query args 
                var_dump($subject, $userData["level"], $userData["class-group"]);
                $sGetClassID->execute([$subject, $userData["level"], $userData["class-group"]]);
                $classID = $sGetClassID->fetchAll(PDO::FETCH_NUM)[0][0];
                $sGetClassID->closeCursor();
                $sAssignClass->execute([$classID, $studentID]);
                $sAssignClass->closeCursor();
            }

            throw new Exception("");
            $this->pdo->commit();
            $result["data"] = $studentID;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e->getMessage();
        }
        return $result;
    }

    public function validateStudent()
    {
        $userData = $_POST;
        $subjects = $userData["subjects"];
        $result = $this->validateUser();

        if ($result["success"] == 1) {
            $classGroup = htmlspecialchars(strtoupper($userData["class-group"] ?? ''));
            $level = htmlspecialchars($userData["level"] ?? '');
            if ($subjects == NULL || empty($subjects))
                $result["errors"][] = "No subjects selected!";
            else {
                $subjects = rtrim(ltrim($subjects, "[\""), "\"]");
                $subjects = str_replace("\"", "", $subjects);
                $subjects =  explode(", ", $subjects);
                $_POST["subjects"] = $subjects;
            }

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
