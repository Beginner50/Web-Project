/*
	2) Stored procedure to add a teacher/admin.
	NOTE: USERID IS AUTO INCREMENTAL THUS NO NEED TO PLACE INSERT.
*/
DELIMITER $$
CREATE PROCEDURE sp_addTeacher(
	IN firstname VARCHAR(64),
	IN lastname VARCHAR(64),
	IN emailAddress VARCHAR(128),
	IN password  VARCHAR(64),
	IN dateOfBirth DATE,
	IN gender CHAR,
    IN subjectTaught VARCHAR(128),
    IN dateJoined DATE
)
BEGIN
	
    DECLARE User_ID INT;
    
    INSERT INTO User(DateOfbirth,FirstName,LastName,Email,Gender,Password)
    VALUES (dateOfBirth,firstname,lastname,emailAddress,gender,password);
    
	SELECT MAX(UserID)
	INTO User_ID FROM user;
    
    IF(ROW_COUNT() = 1) THEN
		INSERT INTO Teacher VALUES (User_ID,subjectTaught,dateJoined);
    END IF;

END$$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE sp_addAdmin(
	IN firstname VARCHAR(64),
	IN lastname VARCHAR(64),
	IN emailAddress VARCHAR(128),
	IN password  VARCHAR(64),
	IN dateOfBirth DATE,
	IN gender CHAR,
    IN dateJoined DATE
)	
BEGIN
	DECLARE User_ID INT;
    
    INSERT INTO User(DateOfbirth,FirstName,LastName,Email,Gender,Password)
    VALUES (dateOfBirth,firstname,lastname,emailAddress,gender,password);
    
	SELECT MAX(UserID)
	INTO User_ID FROM user;
    
    IF(ROW_COUNT() = 1) THEN
		INSERT INTO administrator VALUES (User_ID,dateJoined);
    END IF;
END$$
DELIMITER ;

-- TEST DATA
CALL sp_addTeacher('Umair','Parthasee','abd@example.com','1234','2024-10-11','M','fiziks','2024-10-11');
CALL sp_addTeacher('Prashant','Jatoo','xyz@example.com','456','2024-10-11','M','mass','2024-10-11');
CALL sp_addAdmin('Zorro','D ','qqq@example.com','456','2024-10-11','M','2024-10-11');

SELECT * FROM User;
SELECT * FROM teacher;
SELECT * FROM Administrator;

/*
	4)Stored procedure to check whether login information is valid or not.
*/

    
DROP PROCEDURE sp_checkloginInfo;
DELIMITER $$
CREATE PROCEDURE sp_checkloginInfo(
	IN e_mail VARCHAR(128),
    IN Pass_word VARCHAR(64)
)	
BEGIN
	DECLARE dbPassword VARCHAR(64) DEFAULT NULL;
    DECLARE Status VARCHAR(50);
    
    SELECT Password INTO dbPassword
    FROM User 
    WHERE Email = e_mail
    LIMIT 1;
    
    IF (dbPassword IS NOT NULL) THEN
		SELECT dbPassword;
		IF(dbPassword = Pass_word) THEN
			SET Status = 'Login Successful!';
		ELSE 
			SET Status = 'Incorrect password!';
		END IF;
        
    ELSE
		SET Status = 'Email does not exist!';
    END IF;
    
    SELECT Status;
END$$
DELIMITER ;

--TEST DATA
CALL sp_checkloginInfo('a@example.com','12');
CALL sp_checkloginInfo('xyz@example.com','1');

/*
	6) DONE BY JATOO TOGETHER WITH NUMBER 7
*/

/*
	8) For unauthorized users, admin can validate a teacher or an admin account.
*/

DROP PROCEDURE sp_validate;
DELIMITER $$
CREATE PROCEDURE sp_validate(
	IN Admin_ID INT,
    IN User_ID INT
)	
BEGIN
	DECLARE allowToValidate,isStudent BOOLEAN;
    DECLARE Status VARCHAR(50);
    
    SELECT IsAuthorised INTO allowToValidate
    FROM User 
    WHERE UserID = Admin_ID; 
    
    
    SELECT NOT(isAuthorised) INTO isStudent
    FROM User
    WHERE UserID = 
		(SELECT StudentID 
		FROM Student);
    
    IF (isStudent IS NULL ) THEN
		IF(allowToValidate) THEN
		
			UPDATE User
			SET IsAuthorised = TRUE
			WHERE UserID = User_ID;
			
			SET Status = 'Authentication successful!';
		ELSE
			SET Status = 'This user is NOT allowed to validate';
		END IF;
	ELSE
		SET Status = 'Student CANNOT be validated';
    END IF;
    
    SELECT Status;
END$$
DELIMITER ;

--TEST DATA
CALL sp_validate(3,1);

/*
	10) NEED TO CHANGE WHOLE TABLE: ADD ON DELETE CASCADE TO FOREIGN KEYS
*/

/*
	12)Stored procedure for the teacher to post an announcement. 
  UNTESTED
*/


DELIMITER $$
	CREATE PROCEDURE sp_postannouncement(
		IN Lvl INTEGER,
		IN Class_Group VARCHAR(5),
		IN Subject_Code VARCHAR(5),
        IN Teacher_ID INTEGER,
		IN Message VARCHAR(256)
    )
	BEGIN
		DECLARE CorrectID INTEGER;
        DECLARE Status VARCHAR(50);
        
        SELECT TeacherID INTO CorrectID
        FROM class
        WHERE lvl = level AND ClassGroup = Class_Group AND SubjectCode = Subject_Code;
        
        IF (CorrectID IS NULL) THEN
		
			SET Status = 'This Teacher does not occupy this class';
        ELSE
			IF(CorrectID = Teacher_ID) THEN 
            
				INSERT INTO Class_Message VALUES (lvl,Class_Group,Subject_Code,Message);
                SET Status =  'Announcement posted!';
            END IF;
        END IF;
        
        SELECT Status;
    END$$
	
DELIMITER ;

/*
	14) Stored procedure to allow teachers to view occupied classes. 
  UNTESTED
*/

DELIMITER $$
CREATE PROCEDURE sp_viewOccupiedClass(
	IN Teacher_ID INTEGER
)
BEGIN
	SELECT * FROM Class WHERE Teacher_ID = TeacherID;
END$$
DELIMITER ;

/*
	16) Stored procedure to allow students/teachers to view members of a class. 
  UNTESTED
*/

DELIMITER $$
CREATE PROCEDURE sp_viewClassMembers(
	IN Lvl INTEGER,
	IN Class_Group VARCHAR(5),
	IN Subject_Code VARCHAR(5)
)
BEGIN
	SELECT * FROM class WHERE level = lvl AND ClassGroup = Class_Group AND SubjectCode = Subject_Code;
    SELECT * FROM Class_Student WHERE level = lvl AND ClassGroup = Class_Group AND SubjectCode = Subject_Code;
END$$
