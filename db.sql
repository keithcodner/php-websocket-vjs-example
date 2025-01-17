-- Create the database
CREATE DATABASE websocket_example;

-- Use the database
USE websocket_example;

-- Create the counter table
CREATE TABLE counter (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for the row
    value INT NOT NULL DEFAULT 0       -- Counter value, initialized to 0
);

-- Insert an initial value
INSERT INTO counter (value) VALUES (0);