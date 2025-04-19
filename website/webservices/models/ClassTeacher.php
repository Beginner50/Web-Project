<?php

class ClassTeacher
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllClassesTaughtByTeacherIDs()
    {

        // Get teacherID(s)
        $teacherIDs = array();
        if (isset($_GET['userID']))
            $teacherIDs = [(int)$_GET['userID']];
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

        // Get classes taught
        $result = array_map(function ($teacherID) {
            $stmt = $this->pdo->prepare("SELECT class.ClassID, class.Level, class.ClassGroup, class.SubjectCode FROM teacher 
                                         INNER JOIN class ON teacher.TeacherID = class.TeacherID
                                         WHERE teacher.teacherID = ?
                                         LIMIT ? OFFSET ?;");
            $stmt->bindParam(1, $teacherID);
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            $stmt->bindParam(3, $offset, PDO::PARAM_INT);
            $stmt->execute();

            $classesTaught = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [$teacherID => $classesTaught];
        }, $teacherIDs);
        return ["success" => 1, "data" => $result];
    }
}
