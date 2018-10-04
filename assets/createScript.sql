CREATE TABLE Users (
  username VARCHAR(50) NOT NULL PRIMARY KEY,
  passwd VARCHAR(50) NOT NULL,
  firstName VARCHAR(50) NOT NULL,
  lastName VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  gender CHAR(1) NOT NULL,
  country CHAR(100) NOT NULL,
  profilePicture VARCHAR(1024) -- Stores the path to an image in the filesystem.
);

CREATE TABLE Comments (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  content VARCHAR(300) NOT NULL,
  username VARCHAR(50) NOT NULL,
  commentDate DATETIME NOT NULL,
  repliedCommentId INT,
  FOREIGN KEY (username) REFERENCES Users(username),
  FOREIGN KEY (repliedCommentId) REFERENCES Comments(id)
);

ALTER TABLE Comments AUTO_INCREMENT=1;

CREATE TABLE Followers (
  username1 VARCHAR(50) NOT NULL,
  username2 VARCHAR(50) NOT NULL,
  PRIMARY KEY (username1, username2)
);