/*
	WARNING: CREATE TRIGGER 5) FIRST!!!
    
	Extra: Call this procedure to restore the initial valid state of the database
*/
DELIMITER //
CREATE PROCEDURE webproject.sp_initTables()
BEGIN
	DELETE FROM subject WHERE SubjectCode IN (SELECT SubjectCode FROM Subject);
	DELETE FROM User WHERE UserID IN (SELECT UserID FROM User);

	-- Create the procedure to add admin here. TEST IT FIRST!
    -- Add a default admin, name can be anything (e.g root, admin)
    INSERT INTO Subject VALUES('CS101', 'Computer Science');
END
// DELIMITER ;

/*
	1) Call this procedure to add a student in the database.
    
    USAGE EXAMPLE:
		CALL sp_addStudent('Jim', 'Jam', 'jimjam@gmail.com', 'password', '2020-09-15', 'M', 1, 'RED');
*/
DELIMITER //
CREATE PROCEDURE webproject.sp_addStudent(
		IN firstname VARCHAR(64),
		IN lastname VARCHAR(32),
		IN emailAddress VARCHAR(64),
		IN password  VARCHAR(16),
		IN dateOfBirth DATE,
		IN gender CHAR,
		IN level SMALLINT,
		IN classGroup VARCHAR(5)
)
BEGIN
	DECLARE userID_ INT;
    INSERT INTO User VALUES(dateOfBirth, firstname, lastname, emailAddress, gender, password, true);
    IF(ROW_COUNT() > 0) THEN
		SELECT MAX(UserID) INTO userID_ FROM User;
		INSERT INTO Student VALUES(userID_, level, classGroup);
	END IF;
END //
DELIMITER ;

/*
	3) Call this procedure to let a teacher occupy a class
    
    USAGE EXAMPLE:
    CALL sp_occupyClass(1, 'CS101', 'RED', 1);
*/
DROP PROCEDURE sp_occupyClass;
DELIMITER //
CREATE PROCEDURE webproject.sp_occupyClass(
	teacherID INT,
    subjectTaught VARCHAR(5),
    clsGroup VARCHAR(5),
    lvl SMALLINT)
BEGIN
    UPDATE Class SET TeacherID = teacherID WHERE (SubjectCode = subjectTaught AND ClassGroup = clsGroup AND Level = lvl); 
END
// DELIMITER ;

/*
	5) Trigger to create classes after adding a subject
*/
DELIMITER //
CREATE TRIGGER webproject.tg_createClass
AFTER INSERT ON Subject
FOR EACH ROW
BEGIN
	DECLARE lvl INT;
    SET lvl = 1;
	WHILE lvl < 4 DO
		INSERT INTO Class VALUES(lvl, 'RED', NEW.subjectCode, NULL);
        INSERT INTO Class VALUES(lvl, 'BLUE', NEW.subjectCode, NULL);
		SET lvl = lvl + 1;
    END WHILE;
END
// DELIMITER ;

/*					INCOMPLETE (NEED TO INCLUDE STUDENT, TEACHER & ADMIN DETAILS)
	7) Lists all authorised/unauthorised users
    
    USAGE EXAMPLE:
    CALL sp_listAuthorised(true)        Lists authorised users
    CALL sp_listAuthorised(false)       Lists unauthorised users
*/
DELIMITER //
CREATE PROCEDURE webproject.sp_listAuthorised(
	IN isAuth BOOLEAN)
BEGIN
	SELECT * FROM User WHERE IsAuthorised = isAuth;
	
END
// DELIMITER ;

/*					INCOMPLETE + UNTESTED!!!
	9) A collection of stored procedures to allow a user to update their account information by providing
	   new values in the arguments. Administrators can also update account information of a user.
    
    USAGE EXAMPLE:
    CALL sp_updateAccountInfo(userID, firstname, lastname, password, dateOfBirth)
    CALL sp_updateStudentInfo(studentID, level, classGroup)
    
    Replace parameters you don't want to change with NULL
*/
DELIMITER //
CREATE PROCEDURE sp_updateAccountInfo(
	uID INT,
    fname VARCHAR(64),
    lname VARCHAR(32),
    pass VARCHAR(16),
    dob DATE
)
BEGIN
	IF(fname IS NOT NULL) THEN
		UPDATE User SET Firstname = fname WHERE UserID = uID;
    END IF;
    IF(lname IS NOT NULL) THEN
		UPDATE User SET Lastname = lname WHERE UserID = uID;
    END IF;
    IF(pass IS NOT NULL) THEN
		UPDATE User SET Password = pass WHERE UserID = uID;
    END IF;
    IF(dob IS NOT NULL) THEN
		UPDATE User SET DateOfBirth = dob WHERE UserID = uID;
    END IF;
END
// DELIMITER ;
DELIMITER //
CREATE PROCEDURE sp_updateStudentInfo(
	IN uID INT,
    IN lvl SMALLINT,
    IN clsGroup VARCHAR(5))
BEGIN
	IF (lvl IS NOT NULL) THEN
		UPDATE Student SET Level = lvl WHERE StudentID = uID;
	END IF;
    IF (clsGroup IS NOT NULL) THEN
		UPDATE Student SET ClassGroup = clsGroup WHERE StudentID = uID;
    END IF;
END
// DELIMITER ;

/*					UNTESTED!!!
	11) Stored procedure to view/display information about any user.
		'self' tells the procedure that the current user is viewing their account and will show sensitive information.
        'admin' tells the procedure that an admin is viewing a user account and will hide sensitive information but
         will show statistics.
         'other' tells the procedure that another user is viewing the account and will hide sensitive information
    
    USAGE EXAMPLE:
	CALL sp_viewUser(userID, 'self');
    CALL sp_viewUser(userID, 'admin');
    CALL sp_viewUser(userID, 'other');
*/
DELIMITER //
CREATE PROCEDURE sp_viewUser(
	uID INT,
    viewer VARCHAR(5)
)
BEGIN
	DECLARE fname VARCHAR(64);
    DECLARE lname VARCHAR(32);
    DECLARE dob DATE;
    DECLARE email VARCHAR(64);
    DECLARE gender CHAR;
    DECLARE pass VARCHAR(16);
    DECLARE isAuth BOOLEAN;
    DECLARE lvl SMALLINT;
    DECLARE clsGroup VARCHAR(5);
    DECLARE subjectTaught VARCHAR(5);
    DECLARE dateJoined DATE;
    DECLARE userType VARCHAR(16);
    
    SELECT Firstname INTO fname FROM User WHERE UserID = uID;
    SELECT Lastname INTO lname FROM User WHERE UserID = uID;
    SELECT DateOfBirth INTO dob FROM User WHERE UserID = uID;
    SELECT Email INTO email FROM User WHERE UserID = uID;
    SELECT Gender INTO gender FROM User WHERE UserID = uID;
    SELECT Password INTO pass FROM User WHERE UserID = uID;
    SELECT IsAuthorised INTO isAuth FROM User WHERE UserID = uID;
    
    IF EXISTS(SELECT * FROM Student WHERE StudentID = uID) THEN
		SET userType = 'Student';
        SELECT Level INTO lvl FROM Student WHERE StudentID = uID;
        SELECT ClassGroup INTO clsGroup FROM Student WHERE StudentID = uID;
    END IF;
    IF EXISTS(SELECT * FROM Teacher WHERE TeacherID = uID) THEN
		SET userType = 'Teacher';
        SELECT SubjectTaught INTO subjectTaught FROM Teacher WHERE TeacherID = uID;
        SELECT DateJoined INTO dateJoined FROM Teacher WHERE TeacherID = uID;
    END IF;
    IF EXISTS(SELECT * FROM Administrator WHERE AdminID = uID) THEN
		SET userType = 'Admin';
        SELECT DateJoined INTO dateJoined FROM Administrator WHERE AdminID = uID;
    END IF;
END
// DELIMITER ;

/*					UNTESTED!!!
	13) Stored Procedure to allow students to view enrolled classes
*/
DELIMITER //
CREATE PROCEDURE sp_viewEnrolledClasses(
	studentID INT)
BEGIN
	SELECT * FROM Class INNER JOIN Class_Student ON Class.SubjectCode = Class_Student.SubjectCode WHERE StudentID = studentID;
END
// DELIMITER ;

/*					UNTESTED!!!
	15) Stored Procedure to allow users to get messages of a class
*/
DELIMITER //
CREATE PROCEDURE sp_viewClassMessages(
	subCode VARCHAR(5),
    clsGroup VARCHAR(5),
    lvl SMALLINT)
BEGIN
	SELECT Message FROM Class_Message WHERE SubjectCode = subCode AND ClassGroup = clsGroup AND Level = lvl;
END
// DELIMITER ;

