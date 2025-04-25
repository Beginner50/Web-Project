<?php
class ClassMessage
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMessagesByClassIDs($classID = 0, $limit = 10, $offset = 0)
    {
        $classIDs = [];
        if ($classID == 0) {
            $response = json_decode(file_get_contents("http://localhost/classes?limit=999"), true);
            if (!$response["success"]) return $response;

            $classIDs = array_values(array_map(function ($class) {
                return $class["ClassID"];
            }, $response["data"]));
        } else
            $classIDs = [$classID];


        $classMessagesByClassIDs = array_values(array_filter(array_map(function ($classID) use ($limit, $offset) {
            $stmt = $this->pdo->prepare("SELECT user.UserID, FirstName, LastName, DateSent, Message FROM class_message
                                         INNER JOIN user ON class_message.UserID = user.UserID
                                        WHERE ClassID = ? 
                                        LIMIT ? OFFSET ?");
            $stmt->bindParam(1, $classID, PDO::PARAM_INT);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            $stmt->bindParam(3, $offset, PDO::PARAM_INT);
            $stmt->execute();

            $countStmt = $this->pdo->prepare("SELECT COUNT(*) AS MessageCount 
                                              FROM class_message
                                              WHERE ClassID = ?");
            $countStmt->execute([$classID]);
            $count = $countStmt->fetchAll(PDO::FETCH_ASSOC)[0]["MessageCount"];

            $classMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($classMessages) == 0)
                return 0;
            return [
                $classID => [
                    "Messages" => $classMessages,
                    "pagination" => [
                        "limit" => $limit,
                        "offset" => $offset,
                        "count" => count($classMessages),
                        "total" => $count
                    ]
                ]
            ];
        }, $classIDs), function ($elem) {
            if ($elem == 0)  return false;
            return true;
        }));

        return [
            "success" => 1,
            "data" => $classMessagesByClassIDs
        ];
    }

    public function create($classID, $userID, $message)
    {
        $result = ["success" => 1, "errors" => array()];

        // Validation
        if ($classID == NULL) $result["errors"][] = "classID cannot be empty!";
        else if ($userID == NULL) $result["errors"][] = "userID cannot be empty!";
        else if (strlen($message) == 0) $result["errors"][] = "Message cannot be empty!";
        if (count($result["errors"]) > 0) {
            $result["success"] = 0;
            return $result;
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO class_message(ClassID, UserID, DateSent, Message)
                                         VALUES(?, ?, NOW(), ?); ");
            $stmt->execute([$classID, $userID, $message]);

            $this->pdo->commit();
            return ["success" => 1];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            return ["success" => 0, "errors" => array($e->getMessage())];
        }
    }
}
