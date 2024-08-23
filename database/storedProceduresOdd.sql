/*
	WARNING: CREATE TRIGGER 5) FIRST!!! AND DISABLE SAFE UPDATES
    
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
END;
// DELIMITER ;

/*
	1) Call this procedure to add a student in the database.
    
    USAGE EXAMPLE:
		CALL sp_addStudent(firstname, lastname, email, password, dateOfBirth, gender, level, classGroup);
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
    INSERT INTO User(DateOfBirth, Firstname, Lastname, Email, Gender, Password, IsAuthorised)
    VALUES(dateOfBirth, firstname, lastname, emailAddress, gender, password, true);
    IF(ROW_COUNT() > 0) THEN
		SELECT MAX(UserID) INTO userID_ FROM User;
		INSERT INTO Student VALUES(userID_, level, classGroup);
	END IF;
END //
DELIMITER ;

/*
	3) Call this procedure to let a teacher occupy a class.
    
    USAGE EXAMPLE:
    CALL sp_occupyClass(level, classGroup, level);
*/
DELIMITER //
CREATE PROCEDURE webproject.sp_occupyClass(
	teacherID_ INT,
    clsGroup VARCHAR(5),
    lvl SMALLINT)
BEGIN
	DECLARE subjectTaught VARCHAR(5);
    SELECT SubjectCode INTO subjectTaught FROM Teacher WHERE TeacherID = teacherID_;
    UPDATE Class SET TeacherID = teacherID_ WHERE (SubjectCode = subjectTaught AND ClassGroup = clsGroup AND Level = lvl); 
END;
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
END;
// DELIMITER ;

/*					
	7) Lists all users which meet criteria set by sp argument.
    
	   For the sake of avoiding clustered table display on the website, only the userID, firstname,
       lastname & isAuthorised will be selected. A user is expected to click on a table entry to
       acquire more comprehensive information about the selected user.
    
    USAGE EXAMPLE:
    CALL sp_listUsers('authorised')         Lists authorised users
    CALL sp_listUsers('unauthorised')       Lists unauthorised users
    CALL sp_listUsers('all')       			Lists all users
*/
DELIMITER //
CREATE PROCEDURE webproject.sp_listUsers(
	IN authStatus VARCHAR(16))
BEGIN
	IF (authStatus = 'authorised') THEN
	SELECT UserID, Firstname, Lastname,  isAuthorised FROM User WHERE IsAuthorised = true;
    ELSEIF (authStatus = 'unauthorised') THEN
    SELECT UserID, Firstname, Lastname,  isAuthorised FROM User WHERE IsAuthorised = false;
    ELSEIF (authStatus = 'all') THEN
    SELECT UserID, Firstname, Lastname,  isAuthorised FROM User;
    END IF;
END
// DELIMITER ;

/*					
	9) A stored procedure to allow changing of ANY user attribute. (including isAuthorised which will make its
       own stored procedure redundant)
    
    The sp is intentionally flexible and concise to allow the developer to call it with any attributes
    associated with a user and change them accordingly.
    
    VALID VALUES FOR attributeName: Firstname, Lastname, Password, DateOfBirth, Email, Gender, IsAuthorised
									Level, ClassGroup, SubjectTaught, DateJoined
       
    USAGE EXAMPLE:
    CALL sp_updateAccountInfo(userID, attributeName, newValue);
*/
DROP PROCEDURE sp_updateAccountInfo;
DELIMITER //
CREATE PROCEDURE sp_updateAccountInfo(
	uID INT,
    attributeName VARCHAR(32),
    value VARCHAR(64)
)
BEGIN
	DECLARE STATUS VARCHAR(64);
	DECLARE invalid_cast_error CONDITION FOR SQLSTATE '22003';
    DECLARE EXIT HANDLER FOR invalid_cast_error
	BEGIN
    SELECT 'Invalid value for casting: ', value;
	END;
	
    SET STATUS = 'Invalid/Unchaged value for attribute!';
    
    IF(attributeName = 'Firstname') THEN
    BEGIN
		UPDATE User SET Firstname = value WHERE UserID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'Firstname updated successfully!';
        END IF;
	END;
    ELSEIF(attributeName = 'Lastname') THEN
    BEGIN
		UPDATE User SET Lastname = value WHERE UserID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'Lastname updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'Password') THEN
    BEGIN
		UPDATE User SET Password = value WHERE UserID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'Password updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'DateOfBirth') THEN
    BEGIN
		UPDATE User SET DateOfBirth = CAST(value AS DATE) WHERE UserID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'DateOfBirth updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'Email') THEN
    BEGIN
		UPDATE User SET Email = value WHERE UserID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'Email updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'Gender') THEN
    BEGIN
		UPDATE User SET Gender = CAST(value AS CHAR) WHERE UserID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'Gender updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'IsAuthorised') THEN
    BEGIN
		IF value = 'true' OR value = '1' or value = 'True' THEN
			UPDATE User SET IsAuthorised = 1 WHERE UserID = uID;
        ELSE
			UPDATE User SET IsAuthorised = 0 WHERE UserID = uID;
		END IF;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'IsAuthorised updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'Level') THEN
    BEGIN
		UPDATE Student SET Level = value WHERE StudentID = uID;
		IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'Level updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'ClassGroup') THEN
    BEGIN
		UPDATE Student SET ClassGroup = value WHERE StudentID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'ClassGroup updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'SubjectTaught') THEN
    BEGIN
		UPDATE Teacher SET SubjectTaught = value WHERE TeacherID = uID;
        IF(ROW_COUNT() > 0) THEN
        SET STATUS = 'SubjectTaught updated successfully!';
        END IF;
        END;
	ELSEIF(attributeName = 'DateJoined') THEN
    BEGIN
		UPDATE Teacher SET DateJoined = CAST(value AS DATE) WHERE TeacherID = uID;
        IF(ROW_COUNT() = 0) THEN
			UPDATE Administrator SET DateJoined = CAST(value AS DATE) WHERE AdminID = uID;
            IF(ROW_COUNT() > 0) THEN
				SET STATUS = 'DateJoined updated successfully!';
			END IF;
		ELSE
			SET STATUS = 'DateJoined updated successfully!';
        END IF;
        END;
	ELSE
		SET STATUS = 'Invalid attribute!';
    END IF;
	SELECT STATUS;
END;
// DELIMITER ;


/*					
	11) Stored procedure to view/display information about any user.
		Viewer permissions need to be handled by the backend.
    
    USAGE EXAMPLE:
	CALL sp_viewUser(userID);
*/
DROP PROCEDURE sp_viewUser;
DELIMITER //
CREATE PROCEDURE sp_viewUser(
	uID INT
)
BEGIN
    IF EXISTS(SELECT * FROM Student WHERE StudentID = uID) THEN
		SELECT UserID, Firstname, Lastname, Email, DateOfBirth, Gender, Password, IsAuthorised, Level, ClassGroup, SubjectTaught, Teacher.DateJoined, Administrator.DateJoined
        FROM User LEFT JOIN Student ON User.UserID = StudentID LEFT JOIN Teacher ON User.UserID = Teacher.TeacherID LEFT JOIN Administrator ON User.UserID = Administrator.AdminID
        WHERE UserID = uID;
	END IF;
END;
// DELIMITER ;


/*					UNTESTED!!!
	13) Stored Procedure to allow students to view enrolled classes
*/
DELIMITER //
CREATE PROCEDURE sp_viewEnrolledClasses(
	sID INT)
BEGIN
	SELECT * FROM Class INNER JOIN Class_Student ON Class.SubjectCode = Class_Student.SubjectCode WHERE StudentID = sID;
END
// DELIMITER ;

/*					
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

