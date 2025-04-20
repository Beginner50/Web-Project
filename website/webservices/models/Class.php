<?php

class Classroom
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /*
    URL Query Arguments:
    classID      -  Single class selection
    limit -         Limits selection
    offset -        Offset
    */
    public function getAllClasses()
    {
        $classID = isset($_GET["classID"]) ? (int)$_GET["classID"] : 0;
        $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 10;
        $offset = isset($_GET["offset"]) ? (int)$_GET["offset"] : 0;

        try {
            // Get classes
            $stmt = $this->pdo->prepare("SELECT * FROM class "
                . ($classID != 0 ? ("WHERE ClassID = " . $classID) : "")
                . " LIMIT ? OFFSET ?;");
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $classes = $stmt->fetchAll();

            // Get count classes
            if ($classID == 0) {
                $countStmt = $this->pdo->prepare("SELECT COUNT(*) AS Count FROM class;");
                $countStmt->execute();
                $countClasses = $countStmt->fetchAll()[0]["Count"];
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

    public function create() {}
}
