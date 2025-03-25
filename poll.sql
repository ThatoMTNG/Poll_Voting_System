-- DROP DATABASE `phppoll`;

CREATE DATABASE IF NOT EXISTS `phppoll` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phppoll`;

-- Create the users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `security_question` VARCHAR(255) NOT NULL,
    `security_answer` VARCHAR(255) NOT NULL,
    `user_type` ENUM('admin', 'voter') NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create the polls table
CREATE TABLE IF NOT EXISTS `polls` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `user_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create the poll_answers table
CREATE TABLE IF NOT EXISTS `poll_answers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `poll_id` INT(11) NOT NULL,
    `title` TEXT NOT NULL,
    `votes` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create the votes table
CREATE TABLE IF NOT EXISTS `votes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `poll_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_vote` (`poll_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Example of insertion

-- Insert initial data into the polls table
-- INSERT INTO `polls` (`id`, `title`, `description`, `user_id`) VALUES (1, 'Who\'s your preferred candidate?', '', 1);

-- Insert initial data into the poll_answers table
-- INSERT INTO `poll_answers` (`id`, `poll_id`, `title`, `votes`) VALUES (1, 1, 'Donald Trump', 0), (2, 1, 'Kamala Harris', 0);
