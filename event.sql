CREATE DATABASE event;
USE event;

CREATE TABLE Participants (
    partID INT PRIMARY KEY,
    evCode VARCHAR(50),
    partFName VARCHAR(100),
    partLName VARCHAR(100),
    partDRate DECIMAL(10,2)
);

CREATE TABLE Events (
    evCode VARCHAR(50) PRIMARY KEY,
    evName VARCHAR(150),
    evDate DATE,
    evVenue VARCHAR(150),
    evRFee DECIMAL(10,2)
);

CREATE TABLE Registration (
    regCode INT PRIMARY KEY,
    partID INT,
    regDate DATE,
    regFPaid DECIMAL(10,2),
    regPMode VARCHAR(50),
    FOREIGN KEY (partID) REFERENCES Participants(partID)
);
