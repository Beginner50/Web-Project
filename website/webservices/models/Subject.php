<?php

class Subject
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllSubjects($limit = 20, $offset = 0)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM subject
                                     LIMIT ? OFFSET ? ;");
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM subject;");
        $stmt->execute();
        $total = $stmt->fetchColumn(0);

        return [
            'success' => 1,
            'data' => $subjects,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'count' => count($subjects),
                'total' =>  $total
            ]
        ];
    }

    public function getSubjectsByStudentIDs($userID = 0)
    {
        $userIDs = [];
        if ($userID == 0) {
            $response = json_decode(file_get_contents("http://localhost/users"), true);
            if (!$response["success"])
                return $response;
            $userIDs = array_filter(array_map(function ($user) {
                if ($user["UserType"] != "Student")
                    return 0;
                else
                    return $user["UserID"];
            }, $response["data"]), function ($elem) {
                return $elem == 0 ? false : true;
            });
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
