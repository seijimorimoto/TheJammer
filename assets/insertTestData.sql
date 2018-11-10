-- This is a script for inserting dummy data into the database for testing purposes.

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Deku', '$2y$10$rrGstDsvs2f\/UC7vX8lsGuJ0EpHgjKRuESu3FsLLy8jPVoWPdBT6m', 'Izuku', 'Midoriya', 'deku@ua.com', 'M', 'JP', 'images/Deku_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('All Might', '$2y$10$MTkDkGtMHPpxSNBuhb3wau6Sm9MX.Hj0xl27D.7q8j3UbkvjHDKry', 'Toshinori', 'Yagi', 'allmight@ua.com', 'M', 'JP', 'images/All_Might_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Uravity', '$2y$10$8EsdKaJDWqV4FdMOCtmkMOFGmI9ry9cEQyZHUENHv5PWgScmISKsS', 'Ochako', 'Uraraka', 'uravity@ua.com', 'F', 'JP', 'images/Uravity_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Creati', '$2y$10$DGMhkJ\/klLHtbt8Uqkwqxe26vbzWPdAd15O6NM24vR5uwjTlpIqne', 'Momo', 'Yaoyorozu', 'creati@ua.com', 'F', 'JP', 'images/Creati_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Kacchan', '$2y$10$14rcwTMPehBnyDfhliHzz.OmmhulWMK2YFBft05gfcw5mo\/4wcVPi', 'Katsuki', 'Bakugou', 'kacchan@ua.com', 'M', 'JP', 'images/Kacchan_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Todoroki', '$2y$10$bihFr6qb27cwQO\/8RhVzteQvg7JPt07tmrhB2MTpy0SthyNMY8KPa', 'Shouto', 'Todoroki', 'todoroki@ua.com', 'M', 'JP', 'images/Todoroki_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Froppy', '$2y$10$rCY\/uNAMTyEqZIytsMErBe4zL2064z0m4S.IXnZrniT7PL2DaGJrC', 'Tsuyu', 'Asui', 'froppy@ua.com', 'F', 'JP', 'images/Froppy_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Ingenium', '$2y$10$ChPowVCHtv\/ra3PTLYg34OpkW8WvfxyrtBtEV0uiqX6jZRqzpPOTu', 'Tenya', 'Iida', 'ingenium@ua.com', 'M', 'JP', 'images/Ingenium_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Tsukuyomi', '$2y$10$HQkSv\/gyNtaEAcNQhwmwxufkG\/ujBS7ftsFmei6\/p2nyx0BZ3UYXy', 'Fumikage', 'Tokoyami', 'tsukuyomi@ua.com', 'M', 'JP', 'images/Tsukuyomi_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Jirou', '$2y$10$59cKVJ6EirCQCjZ4f1f\/uOEqTQ701vCDy9HnBtvAY6LfJwRr5IT6y', 'Kyouka', 'Jirou', 'jirou@ua.com', 'F', 'JP', 'images/Jirou_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Eraserhead', '$2y$10$mTzryzpobNkZzPRj4OgIJuy0SjM10oWQuP329Lk10C1jJIccR7u\/i', 'Shouta', 'Aizawa', 'eraserhead@ua.com', 'M', 'JP', 'images/Eraserhead_profile_pic.jpg');

INSERT INTO Comments (content, username, commentDate)
VALUES (
  "Wow... I met someone amazing... someone that could possibly reach All Might's level. I'm talking
  about you, Mirio Togata!",
  'Deku',
  STR_TO_DATE('29/09/2018 12:30', '%d/%m/%Y %H:%i')
);

INSERT INTO Comments (content, username, commentDate)
VALUES (
  "Goodbye One For All... Now it's your turn...",
  'All Might',
  STR_TO_DATE('16/06/2018 21:42', '%d/%m/%Y %H:%i')
);

INSERT INTO Comments (content, username, commentDate)
VALUES (
  "Can't wait to finish the hero provisional license exam. Class 1-A will rock!!!",
  'Uravity',
  STR_TO_DATE('04/08/2018 13:25', '%d/%m/%Y %H:%i')
);

INSERT INTO Friends (username1, username2, requestAccepted)
VALUES ('Deku', 'All Might', 1);

INSERT INTO Friends (username1, username2, requestAccepted)
VALUES ('Deku', 'Uravity', 1);