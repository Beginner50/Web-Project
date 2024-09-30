-- Active: 1727685265847@@127.0.0.1@3306@web_project
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