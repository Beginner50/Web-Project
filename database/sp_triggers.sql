-- Active: 1728142795858@@127.0.0.1@3306@web_project
DELIMITER / /

CREATE PROCEDURE sp_addMessage(
    clsID INT,
    uID INT,
    Message VARCHAR(256)
)
BEGIN
    INSERT INTO class_message(ClassID, UserID, DateSent, Message) VALUES(
        clsID,
        uID,
        NOW(),
        Message
    );
END
//

DELIMITER

DELIMITER / /

CREATE PROCEDURE sp_getListUsers()
BEGIN
    (SELECT UserID, "Student" AS UserType, CONCAT(Firstname, " ", Lastname) AS Name, 1 AS Authorisation FROM user WHERE UserID IN(SELECT StudentID FROM student))
    UNION
    (SELECT user.UserID, UserType, CONCAT(Firstname, " ", Lastname) AS Name, IsApproved FROM user INNER JOIN approval ON user.UserID = approval.UserID);
END;

/ /

DELIMITER