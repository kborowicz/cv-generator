## baza danych 3NF
```sql
/* PROJEKTY */

CREATE TABLE `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(45) NOT NULL UNIQUE,
    `creation_date` DATE NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT '1'
);

/* GRUPY UŻYTKOWNIKÓW */

CREATE TABLE `user_groups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(45) NOT NULL UNIQUE
);

/* UŻYTKOWNICY */

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_id` INT NOT NULL,
    `login` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`group_id`) REFERENCES user_groups(`id`)
);

/* UPRAWNIENIA UŻYTKOWNIKÓW */

CREATE TABLE `user_permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `project_id` INT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES users(`id`),
    FOREIGN KEY (`project_id`) REFERENCES projects(`id`),
    INDEX(`user_id`)
);

/* EMAILE UŻYTKOWNIKÓW */

CREATE TABLE `user_emails` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES users(`id`),
    INDEX(`user_id`)
);

/* STATUSY ZLECEŃ */

CREATE TABLE `status` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(45) NOT NULL
);

/* ZLECENIA */

CREATE TABLE `tasks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `status_id` INT NOT NULL,
    `project_id` INT NOT NULL,
    `created_by_id` INT NOT NULL,
    `assigned_by_id` INT NOT NULL,
    `assigned_to_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `mileage` VARCHAR(255),
    `creation_date` DATETIME NOT NULL,
    `deadline_date` DATE NOT NULL,
    `description` VARCHAR(1000),
    `details` VARCHAR(1000),
    FOREIGN KEY (`status_id`) REFERENCES `status`(`id`),
    FOREIGN KEY (`project_id`) REFERENCES projects(`id`),
    FOREIGN KEY (`created_by_id`) REFERENCES users(`id`),
    FOREIGN KEY (`assigned_by_id`) REFERENCES users(`id`),
    FOREIGN KEY (`assigned_to_id`) REFERENCES users(`id`),
    INDEX(`project_id`)
);

/* PLIKI */

CREATE TABLE `files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `task_id` INT NOT NULL,
    `uploaded_by_id` INT NOT NULL,
    `upload_date` DATETIME NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`task_id`) REFERENCES tasks(`id`),
    FOREIGN KEY (`uploaded_by_id`) REFERENCES users(`id`),
    INDEX(`task_id`)
);

/* INFO O ZLECENIU */

CREATE VIEW tasks_full AS
SELECT
    t.`id`,
    s.`name` as `status`,
    t.`title`,
    t.`mileage`,
    t.`description`,
    t.`details`,
    t.`creation_date`,
    t.`deadline_date`,
    CONCAT(u1.`name`, ' ', u1.`lastname`) AS `created_by`,
    CONCAT(u2.`name`, ' ', u2.`lastname`) AS `assigned_by`,
    CONCAT(u3.`name`, ' ', u3.`lastname`) AS `assigned_to`
FROM `tasks` t
    INNER JOIN `status` s ON s.`id` = t.`status_id`
    INNER JOIN `users` u1 ON u1.`id` = t.`created_by_id` 
    INNER JOIN `users` u2 ON u2.`id` = t.`assigned_by_id` 
    INNER JOIN `users` u3 ON u3.`id` = t.`assigned_by_id`

/* POŁĄCZENIE USERÓW, NP DLA ZAKŁADKI EDIT USER*/

CREATE VIEW users_full AS
SELECT
    u.*,
    g.`name` AS `group_name`,
    e.`email_list`,
    p.`permission_list`,
    p.`permission_list_ids`
FROM `users` u
    INNER JOIN `user_groups` g 
        ON u.`group_id` = g.`id`
    LEFT JOIN (
        SELECT `user_id`, GROUP_CONCAT(`email`) as `email_list`
        FROM `user_emails` 
        GROUP BY `user_id`
    ) e ON e.`user_id` = u.`id`
    LEFT JOIN (
        SELECT 
            `user_id`,
            GROUP_CONCAT(proj.`name`) as `permission_list`,
            GROUP_CONCAT(proj.`id`) as `permission_list_ids`
        FROM `user_permissions` perm
            INNER JOIN `projects` proj ON proj.`id` = perm.`project_id`
        GROUP BY `user_id`
    ) p ON p.`user_id` = u.`id`


SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = 'tasks-rev' AND /*ta linia jest nie potrzebna jezeli*/
    TABLE_NAME = 'tasks'AND
    REFERENCED_COLUMN_NAME IS NOT NULL;

```