
/*
Changes to ERD:
- Removed UnauthorisedUser table since unauthorised users can be represented in
User table with new attribute AuthorisationType. approval table is only meant
to log which administrator approved a certain user.

Explanation to database structure:
- Classes cannot be created by any of the users. They are only created when a subject
is inserted into the Subject table. Therefore, it is important to create trigger
tg_createClasses to simplify insert statements.

- For the database to be valid, its initial state must consist of an admin.

- Subjects need to be inserted into the database before students and teachers are added.

- A student does not need to be authenticated by admin, but teachers and other admins do.
*/

CREATE TABLE subject (
    SubjectCode VARCHAR(5) NOT NULL,
    SubjectName VARCHAR(128),
    PRIMARY KEY (SubjectCode)
);


CREATE TABLE user (
    UserID INTEGER NOT NULL AUTO_INCREMENT,
    DateOfBirth DATE NOT NULL,
    FirstName VARCHAR(64),
    LastName VARCHAR(32),
    Email VARCHAR(64) UNIQUE,
    Gender CHAR,
    Password VARCHAR(125),
 
 
    CHECK (Gender IN ('M', 'F')),
    CHECK (Email like('%@%.%')),
    PRIMARY KEY (UserID)
);

CREATE TABLE student (
    StudentID INTEGER,
    Level SMALLINT,
    ClassGroup VARCHAR(5),
    CHECK (ClassGroup IN ('RED', 'BLUE')),
    CHECK (
        Level > 0
        AND level < 4
    ),
    PRIMARY KEY (StudentID),
    FOREIGN KEY (StudentID) REFERENCES user (UserID) ON DELETE CASCADE
);

CREATE TABLE teacher (
    TeacherID INTEGER,
    SubjectTaught VARCHAR(5),
    DateJoined DATE,
    PRIMARY KEY (TeacherID),
    FOREIGN KEY (TeacherID) REFERENCES user (UserID) ON DELETE CASCADE
);
/* USE STORED PROCEDURE TO ADD CLASS, CHECK IF ALREADY EXIST*/
CREATE TABLE class (
    ClassID INT  AUTO_INCREMENT ,
    Level SMALLINT,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    TeacherID INTEGER,

    PRIMARY KEY (
        ClassID
    ),
    FOREIGN KEY (TeacherID) REFERENCES teacher (TeacherID) ON DELETE CASCADE,
    FOREIGN KEY (SubjectCode) REFERENCES subject (SubjectCode) ON DELETE CASCADE,
    CHECK (ClassGroup IN ('RED', 'BLUE')),
    CHECK (
        Level > 0
        AND level < 4
    ) -- Ammend the trigger to add class for subject if values for level modified
);

CREATE TABLE class_student (
    ClassID INTEGER,
    StudentID INTEGER,

    PRIMARY KEY (
        ClassID,
        StudentID
    ),
    FOREIGN KEY (ClassID) REFERENCES class (ClassID) ON DELETE CASCADE,
    FOREIGN KEY (StudentID) REFERENCES student (StudentID) ON DELETE CASCADE

);

CREATE TABLE class_message (
    ClassID INTEGER,
    UserID INTEGER,
    DateSent DATETIME,
    Message VARCHAR(256),

    PRIMARY KEY (
        UserID,
        ClassID,
        DateSent
    ),

    FOREIGN KEY (ClassID) REFERENCES class (ClassID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES user (UserID) ON DELETE CASCADE

);

CREATE TABLE administrator (
    AdminID INTEGER,
    DateJoined DATE,
    PRIMARY KEY (AdminID),
    FOREIGN KEY (AdminID) REFERENCES user (UserID) ON DELETE CASCADE
);

CREATE TABLE approval (
    AdminID  INTEGER DEFAULT NULL,
    UserID INTEGER,
    UserType VARCHAR(15),
    IsApproved BOOLEAN DEFAULT FALSE,

	 CHECK (
		UserType IN (
			'teacher',
			'admin'
		)
	),
    PRIMARY KEY (UserID),
    FOREIGN KEY (AdminID) REFERENCES administrator (AdminID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES user (UserID) ON DELETE CASCADE
);

-- Trigger to create all classes for a subject
delimiter //
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
END;
//
delimiter ;

-- Initialisation Data
-- Insert the first user as an admin (root)
INSERT INTO
    user (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password
    )
VALUES (
        '1970-01-01',
        'root',
        'admin',
        'root@email.com',
        'M',
        'rootPass123'
    );

INSERT INTO
     administrator (AdminID, DateJoined) SELECT UserID, CURDATE()
      FROM user WHERE Email = 'root@email.com';

-- Insert some subjects
INSERT INTO subject(SubjectCode, SubjectName)VALUES 
              ('MATH1', 'Mathematics 1'),
              ('ENG1', 'English 1');

-- Insert students
INSERT INTO
    user (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password
    )
VALUES (
        '2005-02-15',
        'John',
        'Doe',
        'john.doe@email.com',
        'M',
        'studentPass123'
    ),
    (
        '2005-05-23',
        'Jane',
        'Smith',
        'jane.smith@email.com',
        'F',
        'studentPass123'
    );

INSERT INTO
    student (StudentID, Level, ClassGroup)
SELECT UserID, 1, 'RED'
FROM user
WHERE
    Email = 'john.doe@email.com';

INSERT INTO
    student (StudentID, Level, ClassGroup)
SELECT UserID, 1, 'BLUE'
FROM user
WHERE
    Email = 'jane.smith@email.com';

-- Insert unauthorised and authorised teachers
-- Unauthorised teachers are not yet recognised and hence are not added into Teacher table.
INSERT INTO
    user (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password
    )
VALUES (
        '1980-03-10',
        'Mark',
        'Twain',
        'mark.twain@email.com',
        'M' ,
        'teacherPass123'
    );

INSERT INTO
    user (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password
    )
VALUES (
        '1980-03-10',
        'Kelvin',
        'Lord',
        'lord.kelvin@email.com',
        'M',
        'teacherPass123'
    );

INSERT INTO
    user (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password
    )
VALUES (
        '1980-03-10',
        'Michael',
        'Bron',
        'michael.bron@email.com',
        'M',
        'teacherPass123'
    );

INSERT INTO
    teacher (
        TeacherID,
        SubjectTaught,
        DateJoined
    )
SELECT UserID, 'ENG1', CURDATE()
FROM user
WHERE
    Email = 'michael.bron@email.com';

INSERT INTO
    class_student (
        ClassId,
        StudentID
    )
VALUES (
        1,
        (
            SELECT StudentID
            FROM student
            WHERE
                StudentID = (
                    SELECT UserID
                    FROM user
                    WHERE
                        Email = 'john.doe@email.com'
                )
        )
    ),
    (
        2,  -- Assuming ClassID 2 is appropriate for Jane Smith
        (
            SELECT StudentID
            FROM student
            WHERE
                StudentID = (
                    SELECT UserID
                    FROM user
                    WHERE
                        Email = 'jane.smith@email.com'
                )
        )
    );

-- Assign teacher to class level= 1, class group= RED and subject = ENG1
UPDATE class
SET
    teacherID = (
        SELECT TeacherID
        FROM teacher
        WHERE TeacherID=6
    )
WHERE
    Level = 1 AND ClassGroup = 'RED' AND SubjectCode = 'ENG1';
    
-- Admin approves the unauthorised teacher: Mark Twain
INSERT INTO
    approval (AdminID, UserID)
VALUES (
        (
            SELECT AdminID
            FROM  administrator
            WHERE
                AdminID = (
                    SELECT UserID
                    FROM user
                    WHERE
                        Email = 'root@email.com'
                )
        ),
        (
            SELECT UserID
            FROM user
            WHERE
                Email = 'mark.twain@email.com'
        )
    );


UPDATE approval
SET
    UserType = 'Teacher',
    IsApproved=true
WHERE
    UserID=4;

-- Add a message to the class
INSERT INTO
    class_message (
        ClassID,
        UserID,
        DateSent,
        Message
    )
VALUES (
        1, -- Assuming ClassID 1 is the correct class
        (SELECT UserID FROM user WHERE Email = 'michael.bron@email.com'), -- Teacher's UserID
        NOW(),
        'Welcome to the class'
    );

-- INSERTING ALL SUBJECTS
INSERT INTO subject (SubjectCode, SubjectName)
VALUES 
    ('CS101', 'Introduction to Computer Science'),
    ('MA102', 'Calculus I'),
    ('PH103', 'Physics for Engineers'),
    ('CS201', 'Data Structures'),
    ('CS202', 'Database Systems'),
    ('MA203', 'Linear Algebra'),
    ('PH204', 'Electromagnetism'),
    ('CS301', 'Operating Systems'),
    ('CS302', 'Software Engineering'),
    ('MA304', 'Discrete Mathematics'),
    ('CS305', 'Computer Networks'),
    ('CS306', 'Artificial Intelligence');

