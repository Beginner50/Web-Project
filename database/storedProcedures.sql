-- Procedures must start with DELIMITER // and end with // DELIMITER ; (Space character is necessary in DELIMETER ;)

/*
	Call this procedure to restore the initial valid state of the database
*/
DELIMITER //
CREATE PROCEDURE truncate_all_tables()
BEGIN
	SET FOREIGN_KEY_CHECKS = 0;

    TRUNCATE TABLE Subject;
    TRUNCATE TABLE User;
    TRUNCATE TABLE Student;
    TRUNCATE TABLE Teacher;
    TRUNCATE TABLE Class;
    TRUNCATE TABLE ClassMessage;
    TRUNCATE TABLE ClassStudent;
    TRUNCATE TABLE Admin;
    TRUNCATE TABLE Approval;

  SET FOREIGN_KEY_CHECKS = 1;
END
// DELIMETER ;

/*
	Call this procedure to add a student in the database.
    Params:    Firstname VARCHAR(32)
			   Lastname VARCHAR(32)
			   EmailAddress VARCHAR(64)
               Password  VARCHAR(16)
               DateOfBirth DATE
               Gender CHAR
               Level SMALLINT
               ClassGroup CHAR
               
	Validation of all parameters will be done by the front-end. The SP adds an additional layer of security
    only in the case the javascript has been compromised. 
*/
DELIMITER //
CREATE PROCEDURE webapp.addStudent(
		IN firstname VARCHAR(32),
		IN lastname VARCHAR(32),
		IN emailAddress VARCHAR(64),
		IN password  VARCHAR(16),
		IN dateOfBirth DATE,
		IN gender CHAR,
		IN level SMALLINT,
		IN classGroup CHAR
)
BEGIN
	DECLARE userID INT;
    SELECT MAX(UserID) + 1 FROM User INTO userID;
    
	IF(emailAddress NOT REGEXP '^[^@]+@[^@]+\.[^@]{2,}$') THEN
		RETURN -1;
	END IF;
    
    INSERT INTO User VALUES(userID, dateOfBirth, firstname, lastname, emailAddress, gender, password, true);
    IF(ROW_COUNT() > 0) THEN
		INSERT INTO Student VALUES(userID, level, classGroup);
	ELSE
		RETURN -1;
	END IF;
    RETURN 0;
END //
DELIMITER ;
