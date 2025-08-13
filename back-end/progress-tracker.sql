
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `lessons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lesson_id_str` VARCHAR(50) NOT NULL UNIQUE,
  `title` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL
);

CREATE TABLE `challenges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `challenge_id_str` VARCHAR(50) NOT NULL UNIQUE,
  `title` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `points` INT NOT NULL DEFAULT 25
);

CREATE TABLE `user_lesson_progress` (
  `user_id` INT NOT NULL,
  `lesson_id` INT NOT NULL,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `lesson_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`lesson_id`) REFERENCES `lessons`(`id`) ON DELETE CASCADE
);

CREATE TABLE `user_challenge_progress` (
  `user_id` INT NOT NULL,
  `challenge_id` INT NOT NULL,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `challenge_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`challenge_id`) REFERENCES `challenges`(`id`) ON DELETE CASCADE
);