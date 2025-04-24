<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();
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
			case "list-classes":
				$userType = $_GET['user-type'] ?? "";
				switch ($userType) {
					case "student":
						$classRestHandler = new ClassRestHandler($pdo);
						$result = $classRestHandler->getClassesEnrolledByStudentIDs();
						break;
					case "teacher":
						$classRestHandler = new ClassRestHandler($pdo);
						$result = $classRestHandler->getClassesTaughtByTeacherIDs();
						break;
				}
				break;
			case "list-subjects":
				if ($_GET['user-type'] == "student") {
					$subjectRestHandler = new SubjectRestHandler($pdo);
					$result = $subjectRestHandler->getAllSubjects();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Subjects not available for user!";
				}
				break;
			case "create":
				if ($method == "POST") {
					$userRestHandler = new UserRestHandler($pdo);
					$result = $userRestHandler->createUser();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Invalid HTTP method!";
				}
				break;
			case "delete":
				$userRestHandler = new UserRestHandler($pdo);
				$result = $userRestHandler->deleteUser();
				break;
			case "update":
				if ($method == "POST") {
					$_POST["self-userID"] = $_SESSION["UserID"] ?? $_POST["self-userID"];
					$_POST["self-user-type"] = $_SESSION["UserType"] ?? $_POST["self-user-type"];

					$userRestHandler = new UserRestHandler($pdo);
					$result = $userRestHandler->editUser();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Invalid HTTP method!";
				}
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
				header("Access-Control-Allow-Origin: http://eduportal.net");
				header("Content-Type: application/json");
				header("Access-Control-Allow-Methods: GET, OPTIONS");
				header("Access-Control-Allow-Headers: Content-Type, Authorization");

				$classRestHandler = new ClassRestHandler($pdo);
				$result = $classRestHandler->getAllClasses();
				break;
			case "list-members":
				$classRestHandler = new ClassRestHandler($pdo);
				$result = $classRestHandler->getClassMembersByClassIDs();
				break;
			case "list-messages":
				header("Access-Control-Allow-Origin: http://eduportal.net");
				header("Content-Type: application/json");
				header("Access-Control-Allow-Methods: GET, OPTIONS");
				header("Access-Control-Allow-Headers: Content-Type, Authorization");

				$classRestHandler = new ClassRestHandler($pdo);
				$result = $classRestHandler->getClassMessagesByClassIDs();
				break;
			case "enroll":
				if ($method == "POST") {
					$classRestHandler = new ClassRestHandler($pdo);
					$result = $classRestHandler->enrollStudentInClasses();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Invalid HTTP method!";
				}
				break;
			case "unenroll":
				$classRestHandler = new ClassRestHandler($pdo);
				$result = $classRestHandler->unenrollStudentFromClasses();
				break;
			case "assign":
				if ($method == "POST") {
					header("Access-Control-Allow-Origin: http://eduportal.net");
					header("Content-Type: application/json");
					header("Access-Control-Allow-Methods: GET, OPTIONS");
					header("Access-Control-Allow-Headers: Content-Type, Authorization");

					$classRestHandler = new ClassRestHandler($pdo);
					$result = $classRestHandler->assignTeacher();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Invalid HTTP method!";
				}
				break;
		}
		break;
	case "subject":
		switch ($action) {
			case "list":
				header("Access-Control-Allow-Origin: http://eduportal.net");
				header("Content-Type: application/json");
				header("Access-Control-Allow-Methods: GET, OPTIONS");
				header("Access-Control-Allow-Headers: Content-Type, Authorization");

				$subjectRestHandler = new SubjectRestHandler($pdo);
				$result = $subjectRestHandler->getAllSubjects();
				break;
		}
		break;
	case "message":
		switch ($action) {
			case "create":
				if ($method == "POST") {
					header("Access-Control-Allow-Origin: http://eduportal.net");
					header("Content-Type: application/json");
					header("Access-Control-Allow-Methods: GET, OPTIONS");
					header("Access-Control-Allow-Headers: Content-Type, Authorization");

					$classRestHandler = new ClassRestHandler($pdo);
					$result = $classRestHandler->postMessage();
				} else {
					$result["success"] = 0;
					$result["errors"][] = "Invalid HTTP method!";
				}
				break;
		}
		break;
}

echo json_encode($result);
