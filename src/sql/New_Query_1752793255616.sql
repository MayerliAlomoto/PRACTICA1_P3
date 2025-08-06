CREATE TABLE author (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    orcid VARCHAR(20) NOT NULL,
    affilation VARCHAR(255) NOT NULL
);

CREATE TABLE publication (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(100) NOT NULL,
    publication_date DATE NOT NULL,
    author_id INT NOT NULL,
    type ENUM('book','article') NOT NULL,
    FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE
);

CREATE TABLE book (
    publication_id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    edition VARCHAR(50) NOT NULL,
    Foreign Key (publication_id) REFERENCES publication(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);

CREATE TABLE article (
    publication_id INT AUTO_INCREMENT PRIMARY KEY,
    doi VARCHAR(100) NOT NULL,
    abstract VARCHAR(255) NOT NULL,
    keywords VARCHAR(255) NOT NULL,
    indexation VARCHAR(100) NOT NULL,
    magazine VARCHAR(100) NOT NULL,
    knownlegde_area VARCHAR(100) NOT NULL,
    FOREIGN KEY (publication_id) REFERENCES publication(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);
