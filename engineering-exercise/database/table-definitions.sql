CREATE TABLE `users`
(
    `user_id`       int PRIMARY KEY,
    `email`         varchar(255),
    `phone`         varchar(255),
    `cell`          varchar(255),
    `registered_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `account_age`   int,
    `nat`           varchar(255)
);

CREATE TABLE `identification`
(
    `user_id` int PRIMARY KEY,
    `gender`  varchar(255),
    `title`   varchar(255),
    `first`   varchar(255),
    `last`    varchar(255),
    `dob`     date,
    `age`     int
);

CREATE TABLE `location`
(
    `user_id`     int PRIMARY KEY,
    `street`      varchar(255),
    `city`        varchar(255),
    `state`       varchar(255),
    `country`     varchar(255),
    `postcode`    varchar(255),
    `coordinates` point,
    `offset`      varchar(255),
    `description` varchar(255)
);

CREATE TABLE `picture`
(
    `user_id`   int PRIMARY KEY,
    `large`     varchar(255),
    `medium`    varchar(255),
    `thumbnail` varchar(255)
);

CREATE TABLE `transactions`
(
    `transaction_id` int PRIMARY KEY AUTO_INCREMENT,
    `user_id`        int,
    `timestamp`      timestamp DEFAULT CURRENT_TIMESTAMP,
    `amount`         decimal(5, 2),
    `status`         varchar(255),
    `payment_method` varchar(255)
);

ALTER TABLE `identification`
    ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `location`
    ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `picture`
    ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `transactions`
    ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
