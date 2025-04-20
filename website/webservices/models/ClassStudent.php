<?php

class ClassStudent
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    /*
        URL Query Arguments:
        userID - Single user selection
    */
    public function getAllClassesEnrolledByStudentIDs()
    {
        // Get studentID(s)
        $studentIDs = array();
        if (isset($_GET['userID']))
            $studentIDs = [(int)$_GET['userID']];
        else {
            $response = json_decode(file_get_contents("http://localhost/users?limit=999"), true);
            if (!$response["success"])
                return ["success" => 0, "errors" => array("Could not get students!")];

            $students = array_filter($response["data"], function ($user) {
                if ($user["UserType"] == "Student") return true;
                return false;
            });

            $studentIDs = array_map(function ($student) {
                return $student["UserID"];
            }, $students);
        }

        // Get classes enrolled by studentIDs
        $result = array_map(function ($studentID) {
            $stmt = $this->pdo->prepare("SELECT class.*, subject.SubjectName FROM class_student 
                                         INNER JOIN class ON class_student.ClassID = class.ClassID
                                         INNER JOIN subject ON class.SubjectCode = subject.SubjectCode
                                         WHERE class_student.StudentID = ? ;");
            $stmt->execute([$studentID]);
            $classesEnrolled = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [$studentID => $classesEnrolled];
        }, $studentIDs);
        return ["success" => 1, "data" => $result];
    }

    public function getAllStudentsEnrolledByClassIDs() {}

    public function enrollStudentInClass($studentID, $classID)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO class_student(ClassID, StudentID) 
                                         VALUES(?, ?) ON DUPLICATE KEY UPDATE StudentID = ?;");
            $stmt->execute([$classID, $studentID, $studentID]);
            $stmt->closeCursor();

            $this->pdo->commit();
            return ["success" => 1];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            return ["success" => 0, "errors" => array("Could not enroll students in class: " . $e->getMessage())];
        }
    }
}
