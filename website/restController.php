<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once("connect.php");
require_once("webservices/userRestHandler.php");
require_once("webservices/classRestHandler.php");
require_once("webservices/subjectRestHandler.php");

$method = $_SERVER['REQUEST_METHOD'];
$result = ["success" => 0, "errors" => array()];

if (isset($_GET["resource"]))
	$resource = $_GET["resource"];
if (isset($_GET["action"]))
	$action = $_GET["action"];

switch ($resource) {
	case "user":
		switch ($action) {
			case "list":
				$userRestHandler = new UserRestHandler($pdo);
				$result = $userRestHandler->getAllUsers();
				break;
			case "create":
				if ($method == "POST") {
					// Clean & Validate POST request
					if (isset($_POST["admin-date-joined"])) {
						$_POST["date-joined"] = $_POST["admin-date-joined"];
						unset($_POST["admin-date-joined"]);
					} else if (isset($_POST["teacher-date-joined"])) {
						$_POST["date-joined"] = $_POST["teacher-date-joined"];
						unset($_POST["teacher-date-joined"]);
					}
					$_POST["subjects"] = json_decode($_POST["subjects"], true);

					$userRestHandler = new UserRestHandler($pdo);
					$result = $userRestHandler->addUser();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Invalid HTTP method!";
				}
				break;
			case "delete":
				break;
			case "update":
				break;
			case "authenticate":
				if ($method == "POST") {
					$userRestHandler = new UserRestHandler($pdo);
					$result = $userRestHandler->authenticateUser();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Invalid HTTP method!";
				}
				break;
		}
		break;
	case "classes":
		switch ($action) {
			case "list":
				$userType = isset($_GET['user-type']) ?? "";
				switch ($userType) {
					case "student":
						$classRestHandler = new ClassRestHandler($pdo);
						$result = $classRestHandler->getClassesEnrolledByStudentIDs();
						break;
					case "teacher":
						$classRestHandler = new ClassRestHandler($pdo);
						$result = $classRestHandler->getClassesTaughtByTeacherIDs();
						break;
					default:
						$classRestHandler = new ClassRestHandler($pdo);
						$result = $classRestHandler->getAllClasses();
						break;
				}
				break;
		}
		break;
	case "subject":
		switch ($action) {
			case "list":
				$subjectRestHandler = new SubjectRestHandler($pdo);
				$result = $subjectRestHandler->getAllSubjects();
				break;
		}
		break;
}

echo json_encode($result);
