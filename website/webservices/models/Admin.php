<?php
require_once "User.php";

class Admin extends User
{
    public function getAllAdmins()
    {
        $result = $this->getAllUsers("admin");
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

    public function addAdmin()
    {
        $result = $this->validateAdmin();
        if (!$result["success"])
            return $result;

        $this->pdo->beginTransaction();
        try {
            $userData = $_GET["user"];
            $adminID = $this->addUser($userData, true);

            $sInsertAdmin = $this->pdo->prepare('INSERT INTO administrator(AdminID, DateJoined) VALUES(?, ?);');
            $sInsertAdmin->execute([$adminID,  $userData["datejoined"]]);
            $sInsertAdmin->closeCursor();

            $this->pdo->commit();
            $result["data"]["userID"] = $adminID;
        } catch (Exception $e) {
            var_dump($e);
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            $result["success"] = 0;
            $result["errors"][] = $e;
        }
        return $result;
    }

    public function validateAdmin()
    {
        $userData = $_POST["user"];
        $result = $this->validateUser();

        if ($result["success"] == 1) {
            $dateJoined = htmlspecialchars($userData["dateJoined"] ?? '');

            if (empty($dateJoined)) {
                $result["errors"][] = "Date joined cannot be blank!";
            } elseif (!strtotime($dateJoined)) {
                $result["errors"][] = "Invalid date format!";
            }
        }
        if (sizeof($result["errors"]) > 0)
            $result["success"] = 0;
        return $result;
    }
}
