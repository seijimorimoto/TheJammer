-- This is a script for inserting dummy data into the database for testing purposes.

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Deku', 'oneforall', 'Izuku', 'Midoriya', 'deku@ua.com', 'M', 'JP', 'Deku_profile_pic');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('All Might', 'smash', 'Toshinori', 'Yagi', 'allmight@ua.com', 'M', 'JP', 'All_Might_profile_pic');

INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country, profilePicture)
VALUES ('Uravity', 'float', 'Ochako', 'Uraraka', 'uravity@ua.com', 'F', 'JP', 'Uravity_profile_pic');

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

INSERT INTO Followers (username1, username2)
VALUES ('Deku', 'All Might');

INSERT INTO Followers (username1, username2)
VALUES ('Deku', 'Uravity');