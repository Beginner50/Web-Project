DROP DATABASE webproject;

CREATE DATABASE WEBPROJECT;
USE WEBPROJECT;

CREATE TABLE Subject(
SubjectCode VARCHAR(5) NOT NULL,
SubjectName VARCHAR(128),
PRIMARY KEY(SubjectCode)
);

CREATE TABLE User(
UserID INTEGER NOT NULL AUTO_INCREMENT,
DateOfBirth DATE NOT NULL,
FirstName VARCHAR(64),
LastName VARCHAR(32),
Email VARCHAR(64) UNIQUE,
Gender CHAR,
Password VARCHAR(16),
IsAuthorised BOOLEAN DEFAULT FALSE,

CHECK(Gender IN('M', 'F')),
CHECK (Email like('%@%.%')),
PRIMARY KEY(UserID)
);

CREATE TABLE Student(
StudentID INTEGER,
Level SMALLINT,
ClassGroup VARCHAR(5),

CHECK(ClassGroup IN('RED', 'BLUE')),
CHECK (Level > 0 AND level < 4),

PRIMARY KEY(StudentID),
FOREIGN KEY(StudentID) REFERENCES User(UserID) ON DELETE CASCADE
);

CREATE TABLE Teacher(
TeacherID INTEGER,
SubjectTaught VARCHAR(5),
DateJoined DATE,

PRIMARY KEY(TeacherID),
FOREIGN KEY(TeacherID) REFERENCES User(UserID) ON DELETE CASCADE
);

CREATE TABLE Class(
	
    Level SMALLINT,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    TeacherID INTEGER,
    
    PRIMARY KEY(Level,ClassGroup, SubjectCode),
    FOREIGN KEY(TeacherID) REFERENCES Teacher(TeacherID) ON DELETE CASCADE,
    FOREIGN KEY(SubjectCode) REFERENCES Subject(SubjectCode) ON DELETE CASCADE,
	
    CHECK(ClassGroup IN('RED', 'BLUE')),
	CHECK (Level > 0 AND level < 4)      -- Change trigger to add class if level modified
    
);

CREATE TABLE Class_Student(
	Level SMALLINT,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
	StudentID INTEGER,
    
    PRIMARY KEY(Level,ClassGroup, SubjectCode),
    FOREIGN KEY(SubjectCode) REFERENCES Class(SubjectCode) ON DELETE CASCADE,
	FOREIGN KEY(StudentID) REFERENCES Student(StudentID) ON DELETE CASCADE,
     
    CHECK(ClassGroup IN('RED', 'BLUE')),
	CHECK (Level > 0 AND level < 4)
);

CREATE TABLE Class_Message(
	
	Level SMALLINT,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    Message VARCHAR(256),
    
    PRIMARY KEY(Level,ClassGroup, SubjectCode, Message),
    FOREIGN KEY(SubjectCode) REFERENCES Class(SubjectCode) ON DELETE CASCADE,
	
	CHECK(ClassGroup IN('RED', 'BLUE')),
	CHECK (Level > 0 AND level < 4)
);

CREATE TABLE Administrator(
	AdminID INTEGER,
	DateJoined DATE,
    
    PRIMARY KEY(AdminID),
    FOREIGN KEY (AdminID) REFERENCES User(UserID) ON DELETE CASCADE

);

CREATE TABLE Approval (
	
    AdminID INTEGER,
    UserID INTEGER,
    
    PRIMARY KEY(AdminID,UserID),
    FOREIGN KEY (AdminID) REFERENCES Administrator(AdminID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE CASCADE
);