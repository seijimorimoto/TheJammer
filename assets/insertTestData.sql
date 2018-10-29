-- This is a script for inserting dummy data into the database for testing purposes.

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Deku', 'oneforall', 'Izuku', 'Midoriya', 'deku@ua.com', 'M', 'JP', 'images/Deku_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('All Might', 'smash', 'Toshinori', 'Yagi', 'allmight@ua.com', 'M', 'JP', 'images/All_Might_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Uravity', 'float', 'Ochako', 'Uraraka', 'uravity@ua.com', 'F', 'JP', 'images/Uravity_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Creati', 'creation', 'Momo', 'Yaoyorozu', 'creati@ua.com', 'F', 'JP', 'images/Creati_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Kacchan', 'explosion', 'Katsuki', 'Bakugou', 'kacchan@ua.com', 'M', 'JP', 'images/Kacchan_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Todoroki', 'halfcoldhalfhot', 'Shouto', 'Todoroki', 'todoroki@ua.com', 'M', 'JP', 'images/Todoroki_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Froppy', 'frog', 'Tsuyu', 'Asui', 'froppy@ua.com', 'F', 'JP', 'images/Froppy_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Ingenium', 'engine', 'Tenya', 'Iida', 'ingenium@ua.com', 'M', 'JP', 'images/Ingenium_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Tsukuyomi', 'darkshadow', 'Fumikage', 'Tokoyami', 'tsukuyomi@ua.com', 'M', 'JP', 'images/Tsukuyomi_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Jirou', 'earphonejack', 'Kyouka', 'Jirou', 'jirou@ua.com', 'F', 'JP', 'images/Jirou_profile_pic.jpg');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Eraserhead', 'erasure', 'Shouta', 'Aizawa', 'eraserhead@ua.com', 'M', 'JP', 'images/Eraserhead_profile_pic.jpg');

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