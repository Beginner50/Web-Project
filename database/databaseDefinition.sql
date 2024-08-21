CREATE DATABASE WEBPROJECT;
USE WEBPROJECT;

CREATE TABLE Subject(
SubjectCode VARCHAR(5) NOT NULL,
SubjectName VARCHAR(128),
PRIMARY KEY(SubjectCode)
);

CREATE TABLE User(
UserID INTEGER NOT NULL AUTO_INCREMENT,
DateOfBirth DATE,
FirstName VARCHAR(64),
LastName VARCHAR(64),
Email VARCHAR(128) UNIQUE,
Gender CHAR,
Password VARCHAR(64),
IsAuthorised BOOLEAN DEFAULT FALSE,

CHECK (Email like('^[^@]+@[^@]+\.[^@]{2,}$')),
PRIMARY KEY(UserID)
);

CREATE TABLE Student(
StudentID INTEGER,
Level SMALLINT,
ClassGroup VARCHAR(5),

CHECK(ClassGroup IN('RED', 'BLUE')),
CHECK (Level > 0 AND level < 4),

PRIMARY KEY(StudentID),
FOREIGN KEY(StudentID) REFERENCES User(UserID)
);

CREATE TABLE Teacher(
TeacherID INTEGER,
SubjectTaught VARCHAR(128),
DateJoined DATE,

PRIMARY KEY(TeacherID),
FOREIGN KEY(TeacherID) REFERENCES User(UserID)
);


CREATE TABLE Class(
	
    Level INTEGER,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    TeacherID INTEGER,
    
    PRIMARY KEY(Level,ClassGroup, SubjectCode),
    FOREIGN KEY(TeacherID) REFERENCES Teacher(TeacherID),
    FOREIGN KEY(SubjectCode) REFERENCES Subject(SubjectCode),
	
    CHECK(ClassGroup IN('RED', 'BLUE')),
	CHECK (Level > 0 AND level < 4)
    
);

CREATE TABLE Class_Student(
	Level INTEGER,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
	StudentID INTEGER,
    
    PRIMARY KEY(Level,ClassGroup, SubjectCode),
    FOREIGN KEY(SubjectCode) REFERENCES Class(SubjectCode),
	FOREIGN KEY(StudentID) REFERENCES Student(StudentID),
     
    CHECK(ClassGroup IN('RED', 'BLUE')),
	CHECK (Level > 0 AND level < 4)
);

CREATE TABLE Class_Message(
	
	Level INTEGER,
    ClassGroup VARCHAR(5),
    SubjectCode VARCHAR(5),
    Message VARCHAR(256),
    
    PRIMARY KEY(Level,ClassGroup, SubjectCode,Message),
    FOREIGN KEY(SubjectCode) REFERENCES Class(SubjectCode),
	
	CHECK(ClassGroup IN('RED', 'BLUE')),
	CHECK (Level > 0 AND level < 4)
);

CREATE TABLE Administrator(
	AdminID INTEGER,
	DateJoined DATE,
    
    PRIMARY KEY(AdminID),
    FOREIGN KEY (AdminID) REFERENCES User(UserID)

);

CREATE TABLE Approval (
	
    AdminID INTEGER,
    UserID INTEGER,
    
    PRIMARY KEY(AdminID,UserID),
    FOREIGN KEY (AdminID) REFERENCES Administrator(AdminID),
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);