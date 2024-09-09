/*
Changes to ERD:
- Removed UnauthorisedUser table since unauthorised users can be represented in
User table with new attribute AuthorisationType. Approval table is only meant
to log which administrator approved a certain user.

Explanation to database structure:
- Classes cannot be created by any of the users. They are only created when a subject
is inserted into the Subject table. Therefore, it is important to create trigger
tg_createClasses to simplify insert statements.

- For the database to be valid, its initial state must consist of an admin.

- Subjects need to be inserted into the database before students and teachers are added.

- A student does not need to be authenticated by admin, but teachers and other admins do.
*/

CREATE TABLE Subject (
    SubjectCode VARCHAR(5) NOT NULL,
    SubjectName VARCHAR(128),
    PRIMARY KEY (SubjectCode)
);

CREATE TABLE User (
    UserID INTEGER NOT NULL AUTO_INCREMENT,
    DateOfBirth DATE NOT NULL,
    FirstName VARCHAR(64),
    LastName VARCHAR(32),
    Email VARCHAR(64) UNIQUE,
    Gender CHAR,
    Password VARCHAR(125),
    AuthorisationType VARCHAR(30),
    CHECK (
        AuthorisationType IN (
            'teacherUnauthorised',
            'teacherAuthorised',
            'adminUnauthorised',
            'adminAuthorised',
            'student'
        )
    ),
    CHECK (Gender IN ('M', 'F')),
    CHECK (Email like('%@%.%')),
    PRIMARY KEY (UserID)
);

CREATE TABLE Student (
    StudentID INTEGER,
    Level SMALLINT,
    ClassGroup VARCHAR(5),
    CHECK (ClassGroup IN ('RED', 'BLUE')),
    CHECK (
        Level > 0
        AND level < 4
    ),
    PRIMARY KEY (StudentID),
    FOREIGN KEY (StudentID) REFERENCES User (UserID) ON DELETE CASCADE
);

CREATE TABLE Teacher (
    TeacherID INTEGER,
    SubjectTaught VARCHAR(5),
    DateJoined DATE,
    PRIMARY KEY (TeacherID),
    FOREIGN KEY (TeacherID) REFERENCES User (UserID) ON DELETE CASCADE
);

CREATE TABLE Class (
    Level SMALLINT,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    TeacherID INTEGER,
    PRIMARY KEY (
        Level,
        ClassGroup,
        SubjectCode
    ),
    FOREIGN KEY (TeacherID) REFERENCES Teacher (TeacherID) ON DELETE CASCADE,
    FOREIGN KEY (SubjectCode) REFERENCES Subject (SubjectCode) ON DELETE CASCADE,
    CHECK (ClassGroup IN ('RED', 'BLUE')),
    CHECK (
        Level > 0
        AND level < 4
    ) -- Ammend the trigger to add class for subject if values for level modified
);

CREATE TABLE Class_Student (
    Level SMALLINT,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    StudentID INTEGER,
    PRIMARY KEY (
        Level,
        ClassGroup,
        SubjectCode
    ),
    FOREIGN KEY (SubjectCode) REFERENCES Class (SubjectCode) ON DELETE CASCADE,
    FOREIGN KEY (StudentID) REFERENCES Student (StudentID) ON DELETE CASCADE,
    CHECK (ClassGroup IN ('RED', 'BLUE')),
    CHECK (
        Level > 0
        AND level < 4
    )
);

CREATE TABLE Class_Message (
    Level SMALLINT,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    Message VARCHAR(256),
    PRIMARY KEY (
        Level,
        ClassGroup,
        SubjectCode,
        Message
    ),
    FOREIGN KEY (SubjectCode) REFERENCES Class (SubjectCode) ON DELETE CASCADE,
    CHECK (ClassGroup IN ('RED', 'BLUE')),
    CHECK (
        Level > 0
        AND level < 4
    )
);

CREATE TABLE Administrator (
    AdminID INTEGER,
    DateJoined DATE,
    PRIMARY KEY (AdminID),
    FOREIGN KEY (AdminID) REFERENCES User (UserID) ON DELETE CASCADE
);

CREATE TABLE Approval (
    AdminID INTEGER,
    UserID INTEGER,
    PRIMARY KEY (AdminID, UserID),
    FOREIGN KEY (AdminID) REFERENCES Administrator (AdminID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES User (UserID) ON DELETE CASCADE
);

-- Trigger to create all classes for a subject
DELIMITER / /

CREATE TRIGGER tg_createClass
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

/ /

DELIMITER;

-- Insert the first user as an admin (root)
INSERT INTO
    User (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password,
        AuthorisationType
    )
VALUES (
        '1970-01-01',
        'root',
        'admin',
        'root@email.com',
        'M',
        'rootPass123',
        'adminAuthorised'
    );

INSERT INTO
    Administrator (AdminID, DateJoined)
SELECT UserID, CURDATE()
FROM User
WHERE
    Email = 'root@email.com';

-- Insert some subjects
INSERT INTO
    Subject (SubjectCode, SubjectName)
VALUES ('MATH1', 'Mathematics 1'),
    ('ENG1', 'English 1');

-- Insert students
INSERT INTO
    User (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password,
        AuthorisationType
    )
VALUES (
        '2005-02-15',
        'John',
        'Doe',
        'john.doe@email.com',
        'M',
        'studentPass123',
        'student'
    ),
    (
        '2005-05-23',
        'Jane',
        'Smith',
        'jane.smith@email.com',
        'F',
        'studentPass123',
        'student'
    );

INSERT INTO
    Student (StudentID, Level, ClassGroup)
SELECT UserID, 1, 'RED'
FROM User
WHERE
    Email = 'john.doe@email.com';

INSERT INTO
    Student (StudentID, Level, ClassGroup)
SELECT UserID, 1, 'BLUE'
FROM User
WHERE
    Email = 'jane.smith@email.com';

-- Insert unauthorised and authorised teachers
-- Unauthorised teachers are not yet recognised and hence are not added into Teacher table.
INSERT INTO
    User (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password,
        AuthorisationType
    )
VALUES (
        '1980-03-10',
        'Mark',
        'Twain',
        'mark.twain@email.com',
        'M',
        'teacherPass123',
        'teacherUnauthorised'
    );

INSERT INTO
    User (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password,
        AuthorisationType
    )
VALUES (
        '1980-03-10',
        'Kelvin',
        'Lord',
        'lord.kelvin@email.com',
        'M',
        'teacherPass123',
        'teacherUnauthorised'
    );

INSERT INTO
    User (
        DateOfBirth,
        FirstName,
        LastName,
        Email,
        Gender,
        Password,
        AuthorisationType
    )
VALUES (
        '1980-03-10',
        'Michael',
        'Bron',
        'michael.bron@email.com',
        'M',
        'teacherPass123',
        'teacherAuthorised'
    );

INSERT INTO
    Teacher (
        TeacherID,
        SubjectTaught,
        DateJoined
    )
SELECT UserID, 'ENG1', CURDATE()
FROM User
WHERE
    Email = 'michael.bron@email.com';

-- Assign students to classes
INSERT INTO
    Class_Student (
        Level,
        ClassGroup,
        SubjectCode,
        StudentID
    )
VALUES (
        1,
        'RED',
        'MATH1',
        (
            SELECT StudentID
            FROM Student
            WHERE
                StudentID = (
                    SELECT UserID
                    FROM User
                    WHERE
                        Email = 'john.doe@email.com'
                )
        )
    ),
    (
        1,
        'BLUE',
        'ENG1',
        (
            SELECT StudentID
            FROM Student
            WHERE
                StudentID = (
                    SELECT UserID
                    FROM User
                    WHERE
                        Email = 'jane.smith@email.com'
                )
        )
    );
-- Assign teacher to class level= 1, class group= RED and subject = ENG1
UPDATE Class
SET
    teacherID = (
        SELECT teacherID
        FROM Teacher
    );

-- Admin approves the unauthorised teacher: Mark Twain
INSERT INTO
    Approval (AdminID, UserID)
VALUES (
        (
            SELECT AdminID
            FROM Administrator
            WHERE
                AdminID = (
                    SELECT UserID
                    FROM User
                    WHERE
                        Email = 'root@email.com'
                )
        ),
        (
            SELECT UserID
            FROM User
            WHERE
                Email = 'mark.twain@email.com'
        )
    );

UPDATE User
SET
    AuthorisationType = 'teacherAuthorised'
WHERE
    Email = 'mark.twain@email.com';

-- Add a message to the class
INSERT INTO
    Class_Message (
        Level,
        ClassGroup,
        SubjectCode,
        Message
    )
VALUES (
        1,
        'BLUE',
        'ENG1',
        (
            SELECT TeacherID
            FROM Class
            WHERE
                Level = 1
                AND ClassGroup = 'BLUE'
                AND SubjectCode = 'ENG1'
        )
    );