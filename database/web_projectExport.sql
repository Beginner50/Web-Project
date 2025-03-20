-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: web_project
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administrator`
--

DROP TABLE IF EXISTS `administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administrator` (
  `AdminID` int NOT NULL,
  `DateJoined` date DEFAULT NULL,
  PRIMARY KEY (`AdminID`),
  CONSTRAINT `administrator_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrator`
--

LOCK TABLES `administrator` WRITE;
/*!40000 ALTER TABLE `administrator` DISABLE KEYS */;
INSERT INTO `administrator` VALUES (1,'2024-09-20'),(15,'2024-10-08'),(16,'2024-10-16'),(113,NULL);
/*!40000 ALTER TABLE `administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval`
--

DROP TABLE IF EXISTS `approval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval` (
  `AdminID` int DEFAULT NULL,
  `UserID` int NOT NULL,
  `UserType` varchar(15) DEFAULT NULL,
  `IsApproved` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`UserID`),
  KEY `AdminID` (`AdminID`),
  CONSTRAINT `approval_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `administrator` (`AdminID`) ON DELETE CASCADE,
  CONSTRAINT `approval_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE,
  CONSTRAINT `approval_chk_1` CHECK ((`UserType` in (_utf8mb4'Teacher',_utf8mb4'Admin')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval`
--

LOCK TABLES `approval` WRITE;
/*!40000 ALTER TABLE `approval` DISABLE KEYS */;
INSERT INTO `approval` VALUES (NULL,99,'Teacher',0),(NULL,111,'Teacher',0),(NULL,112,'Teacher',0),(NULL,113,'Admin',0);
/*!40000 ALTER TABLE `approval` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `ClassID` int NOT NULL AUTO_INCREMENT,
  `Level` smallint DEFAULT NULL,
  `ClassGroup` varchar(5) DEFAULT NULL,
  `SubjectCode` varchar(5) DEFAULT NULL,
  `TeacherID` int DEFAULT NULL,
  PRIMARY KEY (`ClassID`),
  KEY `TeacherID` (`TeacherID`),
  KEY `SubjectCode` (`SubjectCode`),
  CONSTRAINT `class_ibfk_1` FOREIGN KEY (`TeacherID`) REFERENCES `teacher` (`TeacherID`) ON DELETE CASCADE,
  CONSTRAINT `class_ibfk_2` FOREIGN KEY (`SubjectCode`) REFERENCES `subject` (`SubjectCode`) ON DELETE CASCADE,
  CONSTRAINT `class_chk_1` CHECK ((`ClassGroup` in (_utf8mb4'RED',_utf8mb4'BLUE'))),
  CONSTRAINT `class_chk_2` CHECK (((`Level` > 0) and (`level` < 4)))
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
/*!40000 ALTER TABLE `class` DISABLE KEYS */;
INSERT INTO `class` VALUES (1,1,'RED','MATH1',9),(2,1,'BLUE','MATH1',9),(3,2,'RED','MATH1',9),(4,2,'BLUE','MATH1',9),(5,3,'RED','MATH1',9),(6,3,'BLUE','MATH1',9),(7,1,'RED','ENG1',6),(8,1,'BLUE','ENG1',NULL),(9,2,'RED','ENG1',NULL),(10,2,'BLUE','ENG1',14),(11,3,'RED','ENG1',14),(12,3,'BLUE','ENG1',14),(13,1,'RED','CS101',13),(14,1,'BLUE','CS101',13),(15,2,'RED','CS101',NULL),(16,2,'BLUE','CS101',13),(17,3,'RED','CS101',NULL),(18,3,'BLUE','CS101',NULL),(19,1,'RED','MA102',NULL),(20,1,'BLUE','MA102',NULL),(21,2,'RED','MA102',NULL),(22,2,'BLUE','MA102',NULL),(23,3,'RED','MA102',NULL),(24,3,'BLUE','MA102',NULL),(25,1,'RED','PH103',NULL),(26,1,'BLUE','PH103',NULL),(27,2,'RED','PH103',NULL),(28,2,'BLUE','PH103',NULL),(29,3,'RED','PH103',NULL),(30,3,'BLUE','PH103',NULL),(31,1,'RED','CS201',NULL),(32,1,'BLUE','CS201',NULL),(33,2,'RED','CS201',NULL),(34,2,'BLUE','CS201',NULL),(35,3,'RED','CS201',NULL),(36,3,'BLUE','CS201',NULL),(37,1,'RED','CS202',NULL),(38,1,'BLUE','CS202',NULL),(39,2,'RED','CS202',NULL),(40,2,'BLUE','CS202',NULL),(41,3,'RED','CS202',NULL),(42,3,'BLUE','CS202',NULL),(43,1,'RED','MA203',NULL),(44,1,'BLUE','MA203',NULL),(45,2,'RED','MA203',NULL),(46,2,'BLUE','MA203',NULL),(47,3,'RED','MA203',NULL),(48,3,'BLUE','MA203',NULL),(49,1,'RED','PH204',NULL),(50,1,'BLUE','PH204',NULL),(51,2,'RED','PH204',NULL),(52,2,'BLUE','PH204',NULL),(53,3,'RED','PH204',NULL),(54,3,'BLUE','PH204',NULL),(55,1,'RED','CS301',NULL),(56,1,'BLUE','CS301',NULL),(57,2,'RED','CS301',NULL),(58,2,'BLUE','CS301',NULL),(59,3,'RED','CS301',NULL),(60,3,'BLUE','CS301',NULL),(61,1,'RED','CS302',NULL),(62,1,'BLUE','CS302',NULL),(63,2,'RED','CS302',NULL),(64,2,'BLUE','CS302',NULL),(65,3,'RED','CS302',NULL),(66,3,'BLUE','CS302',NULL),(67,1,'RED','MA304',NULL),(68,1,'BLUE','MA304',NULL),(69,2,'RED','MA304',NULL),(70,2,'BLUE','MA304',NULL),(71,3,'RED','MA304',NULL),(72,3,'BLUE','MA304',NULL),(73,1,'RED','CS305',NULL),(74,1,'BLUE','CS305',NULL),(75,2,'RED','CS305',NULL),(76,2,'BLUE','CS305',NULL),(77,3,'RED','CS305',NULL),(78,3,'BLUE','CS305',NULL),(79,1,'RED','CS306',NULL),(80,1,'BLUE','CS306',NULL),(81,2,'RED','CS306',NULL),(82,2,'BLUE','CS306',NULL),(83,3,'RED','CS306',NULL),(84,3,'BLUE','CS306',NULL);
/*!40000 ALTER TABLE `class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_message`
--

DROP TABLE IF EXISTS `class_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_message` (
  `ClassID` int NOT NULL,
  `UserID` int NOT NULL,
  `DateSent` datetime NOT NULL,
  `Message` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`UserID`,`ClassID`,`DateSent`),
  KEY `ClassID` (`ClassID`),
  CONSTRAINT `class_message_ibfk_1` FOREIGN KEY (`ClassID`) REFERENCES `class` (`ClassID`) ON DELETE CASCADE,
  CONSTRAINT `class_message_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_message`
--

LOCK TABLES `class_message` WRITE;
/*!40000 ALTER TABLE `class_message` DISABLE KEYS */;
INSERT INTO `class_message` VALUES (1,2,'2001-02-15 00:00:01','This is testing!'),(1,6,'2024-09-20 11:41:35','Welcome to the class'),(2,9,'2024-10-01 09:47:21','Hello Biden, I need $4 billion to bomb Donesk children'),(2,9,'2024-10-01 15:36:01','This is your first assignment'),(10,13,'2024-10-02 15:19:57','fo'),(13,13,'2024-10-03 10:52:41','fsd'),(13,13,'2024-10-03 12:34:28','Hello world!'),(10,14,'2024-10-02 21:12:08','jil\n'),(10,14,'2024-10-04 20:42:14','fdsaf'),(10,14,'2024-10-05 21:11:17','fasdfsdafsaf'),(11,14,'2024-10-17 16:40:23','fsddsa');
/*!40000 ALTER TABLE `class_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_student`
--

DROP TABLE IF EXISTS `class_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_student` (
  `ClassID` int NOT NULL,
  `StudentID` int NOT NULL,
  PRIMARY KEY (`ClassID`,`StudentID`),
  KEY `StudentID` (`StudentID`),
  CONSTRAINT `class_student_ibfk_1` FOREIGN KEY (`ClassID`) REFERENCES `class` (`ClassID`) ON DELETE CASCADE,
  CONSTRAINT `class_student_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_student`
--

LOCK TABLES `class_student` WRITE;
/*!40000 ALTER TABLE `class_student` DISABLE KEYS */;
INSERT INTO `class_student` VALUES (1,2),(19,2),(67,2),(73,2),(79,2),(3,98),(21,98),(51,98),(69,98),(81,98),(20,106),(44,106),(56,106),(62,106),(74,106),(3,107),(27,107),(45,107),(69,107),(81,107),(9,108),(21,108),(45,108),(51,108),(69,108),(35,109),(41,109),(65,109),(77,109),(83,109),(13,110),(19,110),(43,110),(61,110),(79,110);
/*!40000 ALTER TABLE `class_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `StudentID` int NOT NULL,
  `Level` smallint DEFAULT NULL,
  `ClassGroup` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`StudentID`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE,
  CONSTRAINT `student_chk_1` CHECK ((`ClassGroup` in (_utf8mb4'RED',_utf8mb4'BLUE'))),
  CONSTRAINT `student_chk_2` CHECK (((`Level` > 0) and (`level` < 4)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (2,1,'RED'),(3,1,'BLUE'),(98,2,'RED'),(106,1,'BLUE'),(107,2,'RED'),(108,2,'RED'),(109,3,'RED'),(110,1,'RED');
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject` (
  `SubjectCode` varchar(5) NOT NULL,
  `SubjectName` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`SubjectCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
INSERT INTO `subject` VALUES ('CS101','Introduction to Computer Science'),('CS201','Data Structures'),('CS202','Database Systems'),('CS301','Operating Systems'),('CS302','Software Engineering'),('CS305','Computer Networks'),('CS306','Artificial Intelligence'),('ENG1','English 1'),('MA102','Calculus I'),('MA203','Linear Algebra'),('MA304','Discrete Mathematics'),('MATH1','Mathematics 1'),('PH103','Physics for Engineers'),('PH204','Electromagnetism');
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tg_createClass` AFTER INSERT ON `subject` FOR EACH ROW BEGIN
	DECLARE lvl INT;
    SET lvl = 1;
	WHILE lvl < 4 DO
		INSERT INTO class(Level,ClassGroup,SubjectCode,TeacherID) VALUES(lvl, 'RED', NEW.subjectCode,NULL);
        INSERT INTO class(Level,ClassGroup,SubjectCode,TeacherID) VALUES(lvl, 'BLUE', NEW.subjectCode, NULL);
		SET lvl = lvl + 1;
    END WHILE;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher` (
  `TeacherID` int NOT NULL,
  `SubjectTaught` varchar(5) DEFAULT NULL,
  `DateJoined` date DEFAULT NULL,
  PRIMARY KEY (`TeacherID`),
  CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`TeacherID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher`
--

LOCK TABLES `teacher` WRITE;
/*!40000 ALTER TABLE `teacher` DISABLE KEYS */;
INSERT INTO `teacher` VALUES (6,'ENG1','2024-09-20'),(7,'CS101','2024-09-17'),(9,'CS101','2024-09-09'),(13,'CS101','2024-10-22'),(14,'CS101','2024-10-20'),(99,'CS101','2024-10-29'),(111,'CS101',NULL),(112,'CS101',NULL);
/*!40000 ALTER TABLE `teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `DateOfBirth` date NOT NULL,
  `FirstName` varchar(64) DEFAULT NULL,
  `LastName` varchar(32) DEFAULT NULL,
  `Email` varchar(64) DEFAULT NULL,
  `Gender` char(1) DEFAULT NULL,
  `Password` varchar(125) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`),
  CONSTRAINT `user_chk_1` CHECK ((`Gender` in (_utf8mb4'M',_utf8mb4'F'))),
  CONSTRAINT `user_chk_2` CHECK ((`Email` like _utf8mb4'%@%.%'))
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'1970-01-01','root','admin','root@email.com','M','rootPass123'),(2,'2005-02-15','John','Doe','john.doe@email.com','M','studentPass123'),(3,'2005-05-23','Jane','Smith','jane.smith@email.com','F','studentPass123'),(4,'1980-03-10','Mark','Twain','mark.twain@email.com','M','teacherPass123'),(5,'1980-03-10','Kelvin','Lord','lord.kelvin@email.com','M','teacherPass123'),(6,'1980-03-10','Michael','Bron','michael.bron@email.com','M','teacherPass123'),(7,'2024-09-04','Paul','Carmac','paulcarmac@gmail.com','M','$2y$10$g8GHjFD6YEaR1EWG/tnjde57s43xmT0kITxPEcAk6Ha6BTxS21i9O'),(8,'2024-09-02','Jake','Black','jakeblack@gmail.com','M','$2y$10$Zzwx72W.pVy2HaOEd/vsEuIU/7pW95QFEBMk7pievcSUMV1JdqQmC'),(9,'2024-09-02','Jake','Blake','jakeblake@gmail.com','M','$2y$10$m8QyxbZgvXCuSIU3iBBLl./ShIHGapx2uvwBr3SYEFoI5xuT/Loqm'),(13,'2024-10-09','Volumetrix','Flask','volmetrix@gmail.com','M','$2y$10$zr2oY.O2Z/n7C6HK3BB/Ree5Q6515BB340Gf3YAuQaeI/jXuWpKA.'),(14,'2024-10-12','Ju','Lu','jl@gmail.com','F','$2y$10$u5IHOkdtSBcXIdM.8WOstu45ChmbHAywKtGgdfSGCuiF0UK351MPK'),(15,'2024-10-09','jake','paul','jakepaul@gmail.com','F','$2y$10$eutajFhfX1E3q8hozNj9c.2gd/hPnbAqtjd7wtBhM/ell80MOZq.G'),(16,'2024-10-06','new','admin','na@gmail.com','M','$2y$10$EN1oDtVX4BIaj1.KEUysQeBLWeuXbSaLeUcZ/6YnTjf9YevwbwEkS'),(98,'2017-05-16','Paul','Reeves','pr@gmail.com','M','$2y$10$uMkp0sCONYW2/jhztOnda.IcwKAr.xkGMx4tIjsj0Sli7aqVvT1uW'),(99,'2024-10-30','teacher','Alice','tb@gmail.com','F','$2y$10$CyEss0fcCm6YDqD3yci3ZOk9u9MBxukv.FFbDXgf5/2byvqwKYoqy'),(106,'2025-02-05','Paul','Pierre','paulPierre@gmail.com','M','$2y$10$pSjnU0VRA9xhnao2gs0uBeZQ0aWoXAmB3hSXvrRlALMaQRJnTMoyK'),(107,'2025-02-11','Emanuel','Macro','em@gmail.com','M','$2y$10$qyNjiW.1e/RAo8Hb6WNC6eHOAzoYW89urBNBbmucvVyHxOB3DrjsS'),(108,'2025-02-11','Emanue','Macro','ema@gmail.com','M','$2y$10$no5tEtBrKEXLoydDQ2sn1eNQKgSpZROR6Zj/5R8zGa2LwiLsgtxYG'),(109,'2025-02-11','James','Macro','jam@gmail.com','F','$2y$10$NUR0lePixcifp7N6lCTQq.J1H4826i/cVRw85sT1fn2lYo4lSNrHO'),(110,'2004-10-25','Prashant','Jatoo','jatooprashant099@gmail.com','M','$2y$10$K4Rq6aoW4uo3FofLc3irtOoNEwZ2dVx6oGX.dZgrb9X3lh23ibAc.'),(111,'2025-01-29','password','Redacted2111','password@redacted2111.com','F','$2y$10$KcrYtGo1oVTupesX/RKvHenbohQzA/wwG9r2JTYoTmMqPgXLq.gOe'),(112,'2003-11-21','Prashant','Jatoo','prashant.jatoo@umail.uom.ac.mu','M','$2y$10$l90q/pJ3qHslgEPZEIsmAOjLhKlsoQrHEvBX7dvUAX7e723BG8rCO'),(113,'2025-03-04','Admin','Redacted2111','admin@Redacted2111.com','M','$2y$10$QONKpNMJd.0F5TwDNU/J9.nynyGUIun.ksDfdSK4umgWo65moehRO');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-20 11:45:01
