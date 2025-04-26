<?php
require_once "User.php";

class Admin extends User
{
    public function getAllAdmins($userID, $limit = 10, $offset = 0)
    {
        $result = $this->getAllUsers(userType: "admin", userID: $userID, limit: $limit, offset: $offset);
        if (!$result["success"]) return $result;
        $admins = array_map(function ($u) {
            $userID = $u["UserID"];

            // Get admin data per userID
            $stmt = $this->pdo->prepare("SELECT DateJoined from administrator WHERE AdminID = ?;");
            $stmt->execute([$userID]);
            $adminData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [...$u, ...$adminData[0]];
        }, $result["data"]);

        $result["data"] = $admins;
        return $result;
    }

    public function create($userData, $approval = true)
    {
        $result = $this->validateAdmin($userData, $approval);
        if (!$result["success"])
            return $result;

        try {
            $this->pdo->beginTransaction();
            $adminID = User::create($userData, true)["data"]["UserID"];

            $sInsertAdmin = $this->pdo->prepare('INSERT INTO administrator(AdminID, DateJoined) VALUES(?, ?);');
            $sInsertAdmin->execute([$adminID,  $userData["date-joined"]]);
            $sInsertAdmin->closeCursor();

            $this->pdo->commit();
            $result["data"] = ["UserID" => $adminID, "UserType" => $userData["user-type"]];
        } catch (Exception $e) {
            var_dump($e);
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e;
        }
        return $result;
    }

    public function edit($userData, $approval = true)
    {
        if ($userData["self-userID"] != $userData["userID"] && $userData["self-user-type"] != "admin")
            return ["success" => 0, "errors" => ["Not Authorised!"]];

        try {
            $this->pdo->beginTransaction();
            User::edit($userData, isset($userData["is-approved"]));

            $stmt = $this->pdo->prepare("UPDATE administrator SET DateJoined = ?
                                        WHERE AdminID = ?;");
            $stmt->bindParam(1, $userData["date-joined"]);
            $stmt->bindParam(2, $userData["userID"]);
            $stmt->execute();

            $this->pdo->commit();
            return ["success" => 1, "errors" => []];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            return ["success" => 0, "errors" => [$e->getMessage()]];
        }
    }

    public function validateAdmin($userData)
    {
        if (isset($userData['date-joined'])) {
            $userData['date-joined'] = str_replace('/', '-', $userData['date-joined']);
        }
        $schemaData = json_decode(file_get_contents("schemas/adminSchema.json"));
        return $this->validateUser($schemaData, $userData);
    }
}
