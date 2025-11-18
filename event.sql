CREATE DATABASE events;
USE events;

CREATE TABLE Events (
    evCode INT AUTO_INCREMENT PRIMARY KEY,
    evName VARCHAR(150),
    evDate DATE,
    evVenue VARCHAR(150),
    evRFee DECIMAL(10,2)
);

CREATE TABLE Participants (
    partID INT AUTO_INCREMENT PRIMARY KEY,
    evCode INT,
    partFName VARCHAR(100),
    partLName VARCHAR(100),
    partDRate DECIMAL(10,2),
    FOREIGN KEY (evCode) REFERENCES Events(evCode)
);

CREATE TABLE Registration (
    regCode INT AUTO_INCREMENT PRIMARY KEY,
    partID INT,
    regDate DATE,
    regFPaid DECIMAL(10,2),
    regPMode VARCHAR(50),
    FOREIGN KEY (partID) REFERENCES Participants(partID)
);
