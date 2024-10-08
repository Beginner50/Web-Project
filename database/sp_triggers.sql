-- Active: 1728405159135@@127.0.0.1@3306@web_project
DELIMITER $$

-- Trigger to create all classes for a subject
CREATE TRIGGER tg_createClass
AFTER INSERT ON subject
FOR EACH ROW
BEGIN
	DECLARE lvl INT;
    SET lvl = 1;
	WHILE lvl < 4 DO
		INSERT INTO class(Level,ClassGroup,SubjectCode,TeacherID) VALUES(lvl, 'RED', NEW.subjectCode,NULL);
        INSERT INTO class(Level,ClassGroup,SubjectCode,TeacherID) VALUES(lvl, 'BLUE', NEW.subjectCode, NULL);
		SET lvl = lvl + 1;
    END WHILE;
END $$

DELIMITER $$

CREATE PROCEDURE sp_addMessage(
    clsID INT,
    userID INT,
    Message VARCHAR(256)
)
BEGIN
    INSERT INTO class_message(ClassID, UserID, DateSent, Message) VALUES(
        clsID,
        userID,
        NOW(),
        Message
    );
END $$

DELIMITER $$

CREATE PROCEDURE sp_getListUsers()
BEGIN
    (SELECT UserID, "Student" AS UserType, CONCAT(Firstname, " ", Lastname) AS Name, 1 AS Authorisation FROM user WHERE UserID IN(SELECT StudentID FROM student))
    UNION
    (SELECT user.UserID, UserType, CONCAT(Firstname, " ", Lastname) AS Name, IsApproved FROM user INNER JOIN approval ON user.UserID = approval.UserID);
END $$

DELIMITER $$

-- No transaction required since php side will handle that
CREATE PROCEDURE sp_addStudent(
    fname VARCHAR(64),
    lname VARCHAR(32),
    email VARCHAR(64),
    gender CHAR,
    dateOfBirth DATE,
    password VARCHAR(125),
    classGroup VARCHAR(5),
    level SMALLINT
)
BEGIN
    DECLARE userID SMALLINT;
    DECLARE clsID SMALLINT;

    -- Insert Into User Table
    INSERT INTO user(DateOfBirth, FirstName, LastName,Email,Gender,Password) 
    VALUES(dateOfBirth, fname, lname, email, gender, password);

    -- Insert Into Student Table
    SET userID = LAST_INSERT_ID(); 
    INSERT INTO student(StudentID, Level, ClassGroup) VALUES(userID, level, classGroup);

    SELECT userID AS "StudentID";
END $$

DELIMITER $$

CREATE PROCEDURE sp_addTeacher (
    fname VARCHAR(64),
    lname VARCHAR(32),
    email VARCHAR(64),
    gender CHAR,
    dateOfBirth DATE,
    password VARCHAR(125),
    subjectTaught VARCHAR(5),
    datejoined DATE
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;


    START TRANSACTION;
    -- Insert Into User Table
    INSERT INTO user (DateOfBirth,FirstName,LastName,Email,Gender,Password)
    VALUES ( dateOfBirth, fname, lname, email, gender, password);
    SET @userID = LAST_INSERT_ID();

    -- Insert into teacher table
    INSERT INTO teacher ( TeacherID, SubjectTaught, DateJoined)
    VALUES ( @userID, subjectTaught, dateJoined);

    -- Insert into approval
    INSERT INTO approval ( AdminID, UserID, UserType, IsApproved)
    VALUES (null, @userID, 'Teacher', 0);

    COMMIT;
END $$

DELIMITER $$

CREATE PROCEDURE sp_addAdmin (
    fname VARCHAR(64),
    lname VARCHAR(32),
    email VARCHAR(64),
    gender CHAR,
    dateOfBirth DATE,
    password VARCHAR(125),
    datejoined DATE
)
BEGIN
    DECLARE userID SMALLINT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    -- Insert Into User Table
    INSERT INTO user (DateOfBirth,FirstName,LastName,Email,Gender,Password)
    VALUES ( dateOfBirth, fname, lname, email, gender, password);
    SET userID = LAST_INSERT_ID();

    -- Insert into admin table
    INSERT INTO administrator ( AdminID , DateJoined)
    VALUES ( userID, dateJoined);

    -- Insert into approval
    INSERT INTO approval ( AdminID, UserID, UserType, IsApproved)
    VALUES (null, userID, 'Admin', 0);

    COMMIT;
END $$

DELIMITER;