<?php

class ClassTeacher
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllClassesTaughtByTeacherIDs($userID = 0)
    {
        // Get teacherID(s)
        $teacherIDs = array();
        if ($userID != 0)
            $teacherIDs = [$userID];
        else {
            $response = json_decode(file_get_contents("http://localhost/users?limit=999"), true);
            if (!$response["success"])
                return ["success" => 0, "errors" => array("Could not get teachers!")];

            $teacherIDs = array_filter(array_map(function ($user) {
                if ($user["UserType"] == "Teacher")
                    return $user["UserID"];
                else
                    return 0;
            }, $response["data"]), function ($elem) {
                if ($elem == 0) return false;
                return true;
            });
        }

        $result = array_values(array_map(function ($teacherID) {
            $stmt = $this->pdo->prepare("SELECT ClassID, Level, ClassGroup, class.SubjectCode, SubjectName
                                         FROM class
                                         INNER JOIN subject ON class.SubjectCode = subject.SubjectCode
                                         WHERE TeacherID = ?;");
            $stmt->execute([$teacherID]);

            $classesTaught = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [$teacherID => $classesTaught];
        }, $teacherIDs));

        return [
            "success" => 1,
            "data" => $result
        ];
    }

    public function assignTeacher($userID, $classID)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("UPDATE class SET TeacherID = ? WHERE ClassID = ?;");
            $stmt->execute([$userID, $classID]);
            $stmt->closeCursor();

            $this->pdo->commit();
            return ["success" => 1];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            return ["success" => 0, "errors" => array($e->getMessage())];
        }
    }
}
