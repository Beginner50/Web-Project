<?php

class Subject
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllSubjects()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM subject;");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ["success" => 1, "data" => $subjects];
    }

    public function getSubjectsByStudentIDs($userID = 0)
    {
        $userIDs = [];
        if ($userID == 0) {
            $response = json_decode(
                file_get_contents("http://localhost/users/student/" . ($userID != 0 ? $userID : "")),
                true
            );
            if (!$response["success"])
                return $response;
            $userIDs = array_map(function ($user) {
                return $user["UserID"];
            }, $response["data"]);
        } else
            $userIDs = [$userID];

        $subjectsByStudentIDs = array_map(function ($userID) {
            $response = json_decode(
                file_get_contents("http://localhost/users/student/" . $userID . "/classes"),
                true
            );
            if (!$response["success"])
                return $response;
            $classes = $response["data"];
            $subjects = array_map(function ($class) {
                return [
                    "SubjectCode" => $class["SubjectCode"],
                    "SubjectName" => $class["SubjectName"]
                ];
            }, $classes);
            return [$userID => $subjects];
        }, $userIDs);

        return ["success" => 1, "data" => $userID == 0 ? $subjectsByStudentIDs : $subjectsByStudentIDs[0][$userID]];
    }
}
