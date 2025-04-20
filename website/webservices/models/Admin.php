<?php
require_once "User.php";

class Admin extends User
{
    public function getAllAdmins($userID, $limit, $offset)
    {
        $result = $this->getAllUsers(userType: "admin", userID: $userID, limit: $limit, offset: $offset);
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
            $adminID = User::create($userData, true)["data"]["userID"];

            $sInsertAdmin = $this->pdo->prepare('INSERT INTO administrator(AdminID, DateJoined) VALUES(?, ?);');
            $sInsertAdmin->execute([$adminID,  $userData["date-joined"]]);
            $sInsertAdmin->closeCursor();

            $this->pdo->commit();
            $result["data"] = ["userID" => $adminID, "userType" => $userData["user-type"]];
        } catch (Exception $e) {
            var_dump($e);
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e;
        }
        return $result;
    }

    public function validateAdmin($userData)
    {
        $result = User::validateUser($userData);

        if ($result["success"] == 1) {
            $dateJoined = htmlspecialchars($userData["date-joined"] ?? '');

            if (empty($dateJoined)) {
                $result["errors"][] = "Date joined cannot be blank!";
            } elseif (!strtotime($dateJoined)) {
                $result["errors"][] = "Invalid date format!";
            }
        }
        if (count($result["errors"]) > 0)
            $result["success"] = 0;
        return $result;
    }
}
