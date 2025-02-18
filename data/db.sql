/* sqlite3 app.sqlite */

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    active INT NOT NULL DEFAULT 1,
    last_login DATETIME,
    UNIQUE(username)
);
/* default user: admin, password: supersecret */
INSERT INTO users (id, username, password, active) 
VALUES 
    (1, 'admin', '$2y$10$dafNpt7nugiu07HfOztFIetV1uXJlZacTtUIebVaHzdCDSKdQIQ6i', 1);
    
-- posts definition

CREATE TABLE `posts` ( id INTEGER PRIMARY KEY AUTOINCREMENT , `title` TEXT, `content` TEXT, "published" INTEGER DEFAULT 1 NOT NULL, slug TEXT);

-- categories definition

CREATE TABLE `categories` ( `id` INTEGER PRIMARY KEY AUTOINCREMENT  ,`name` TEXT ,`description` TEXT ,`meta_description` TEXT    );
