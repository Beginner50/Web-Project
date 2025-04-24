<?php

class Classroom
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAllClasses($classID = 0, $limit = 999, $offset = 0)
    {
        try {
            // Get classes
            $stmt = $this->pdo->prepare("SELECT class.*, subject.SubjectName FROM class 
                    INNER JOIN subject ON class.SubjectCode = subject.SubjectCode"
                . ($classID != 0 ? ("WHERE ClassID = " . $classID) : "")
                . " LIMIT ? OFFSET ?;");
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get count classes
            if ($classID == 0) {
                $countStmt = $this->pdo->prepare("SELECT COUNT(*) AS Count FROM class;");
                $countStmt->execute();
                $countClasses = $countStmt->fetchAll(PDO::FETCH_ASSOC)[0]["Count"];
            }

            return [
                "success" => 1,
                "data" => $classes,
                "pagination" => [
                    "limit" => $limit,
                    "offset" => $offset,
                    "count" => count($classes),
                    "total" => ($classID == 0 ? $countClasses : 1),
                ]
            ];
        } catch (PDOException $e) {
            return ["success" => 0, "errors" => array($e->getMessage())];
        }
    }

    public function getClassMembersByClassIDs($classID = 0, $limit = 999, $offset = 0)
    {
        $classIDs = [];
        if ($classID == 0) {
            $response = $this->getAllClasses(limit: $limit, offset: $offset);
            if (!$response["success"]) return $response;
            $classIDs = array_map(function ($class) {
                return $class["ClassID"];
            }, $response["data"]);
        } else
            $classIDs = [$classID];

        $classMembersByClassIDs = array_filter(array_map(function ($classID) {
            $stmt = $this->pdo->prepare("(SELECT 'Student' AS UserType, StudentID AS UserID,
                                     FirstName, LastName
                                     FROM class_student 
                                     INNER JOIN user ON user.UserID = class_student.StudentID
                                     WHERE ClassID = ?)
                                     UNION
                                     (SELECT 'Teacher' AS UserType, TeacherID AS UserID,
                                      FirstName, LastName
                                      FROM class INNER JOIN user ON user.UserID = class.TeacherID
                                      WHERE ClassID = ? AND TeacherID IS NOT NULL)");
            $stmt->execute([$classID, $classID]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($result))
                return [$classID => $result];
            else
                return 0;
        }, $classIDs), function ($elem) {
            if ($elem == 0) return false;
            return true;
        });
        return ["success" => 1, "data" => $classMembersByClassIDs];
    }

    public function create($level, $classGroup, $subjectCode)
    {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO class(Level, ClassGroup, SubjectCode) VALUES(?,?,?)");
            $stmt->execute([$level, $classGroup, $subjectCode]);
            $stmt->closeCursor();

            $classID = $this->pdo->lastInsertId();
            $this->pdo->commit();

            return ["success" => 1, "data" => ["classID" => $classID]];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            return ["success" => 0, "errors" => array("Could not create class: " . $e->getMessage())];
        }
    }

    public function findClassID($level, $classGroup, $subjectCode)
    {
        $stmt = $this->pdo->prepare("SELECT ClassID FROM class WHERE Level = ? AND ClassGroup = ? AND SubjectCode = ?;");
        $stmt->execute([$level, $classGroup, $subjectCode]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result == NULL)
            return ["success" => 0, "errors" => array("Could not find classID!")];

        $classID = $result[0]["ClassID"];
        return ["success" => 1, "data" => ["classID" => $classID]];
    }
}
