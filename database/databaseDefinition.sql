CREATE DATABASE webApp;
USE webApp;

CREATE TABLE Subject(
SubjectCode VARCHAR(5) NOT NULL,
SubjectName VARCHAR(128),
PRIMARY KEY(SubjectCode)
);

CREATE TABLE User(
UserID INTEGER NOT NULL,
DateOfBirth DATE,
FirstName VARCHAR(64),
LastName VARCHAR(64),
Email VARCHAR(128),
Gender CHAR,
Password_ VARCHAR(64),
isAuthorised BOOLEAN,
PRIMARY KEY(UserID)
);

CREATE TABLE Student(
UserID INTEGER,
Level SMALLINT,
ClassGroup CHAR,
CHECK(ClassGroup IN('A', 'B')),
PRIMARY KEY(UserID),
FOREIGN KEY(UserID) REFERENCES User(UserID)
);

CREATE TABLE StudentSubject(
UserID INTEGER,
SubjectCode VARCHAR(5),
PRIMARY KEY(UserID, SubjectCode)
);

CREATE TABLE Teacher(
UserID INTEGER,
SubjectTaught VARCHAR(128),
DateJoined DATE,
PRIMARY KEY(UserID),
FOREIGN KEY(UserID) REFERENCES User(UserID)
);

CREATE TABLE Class(
Level SMALLINT,
ClassGroup CHAR,
SubjectCode VARCHAR(5),
TeacherID INTEGER,
CHECK(ClassGroup IN('A', 'B')),
PRIMARY KEY(Level, ClassGroup, SubjectCode),
FOREIGN KEY(SubjectCode) REFERENCES Subject(SubjectCode),
FOREIGN KEY(TeacherID) REFERENCES Teacher(UserID)
);

CREATE TABLE ClassMessage(
Level SMALLINT,
ClassGroup CHAR,
SubjectCode VARCHAR(5),
Message VARCHAR(256),
PRIMARY KEY(Level, ClassGroup, SubjectCode, Message),
FOREIGN KEY(SubjectCode) REFERENCES Class(SubjectCode),
CHECK(Level > 1 AND Level <=5),
FOREIGN KEY(ClassGroup) REFERENCES Class(ClassGroup)
);

CREATE TABLE ClassStudent(
Level SMALLINT,
ClassGroup CHAR,
SubjectCode VARCHAR(5),
StudentID INTEGER,
PRIMARY KEY(Level, ClassGroup, SubjectCode, StudentID),
FOREIGN KEY(SubjectCode) REFERENCES Class(SubjectCode),
CHECK(Level > 1 AND Level <=5),
FOREIGN KEY(ClassGroup) REFERENCES Class(ClassGroup),
FOREIGN KEY(StudentID) REFERENCES Student(UserID)
);