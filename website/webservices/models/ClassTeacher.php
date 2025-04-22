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
                return ["success" => 0, "errors" => array("Could not get students!")];

            $teachers = array_filter($response["data"], function ($user) {
                if ($user["UserType"] == "Teacher") return true;
                return false;
            });

            $teacherIDs = array_map(function ($teacher) {
                return $teacher["UserID"];
            }, $teachers);
        }

        $result = array_map(function ($teacherID) {
            $stmt = $this->pdo->prepare("SELECT class.ClassID, class.Level, class.ClassGroup, class.SubjectCode FROM teacher 
                                         INNER JOIN class ON teacher.TeacherID = class.TeacherID
                                         WHERE teacher.teacherID = ?;");
            $stmt->execute([$teacherID]);

            $classesTaught = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [$teacherID => $classesTaught];
        }, $teacherIDs);

        return [
            "success" => 1,
            "data" => $userID == 0 ? $result : $result[0][$userID]
        ];
    }
}
