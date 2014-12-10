CREATE TABLE IF NOT EXISTS `user` (
  `id`        INT(10) UNSIGNED NOT NULL,
  `username`  VARCHAR(250)     NOT NULL,
  `email`     VARCHAR(250)     NOT NULL,
  `password`  VARCHAR(250)     NOT NULL,
  `type`      INT(11)          NOT NULL,
  `accountId` INT(11)          NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  AUTO_INCREMENT =5;

INSERT INTO `user` (`id`, `username`, `email`, `password`, `type`, `accountId`) VALUES
  (1, 'author', 'author@example.com', '356a192b7913b04c54574d18c28d46e6395428ab', 1, 3),
  (2, 'executor', 'executor@example.com', '356a192b7913b04c54574d18c28d46e6395428ab', 2, 4),
  (3, 'author2', 'author2@example.com', '356a192b7913b04c54574d18c28d46e6395428ab', 1, 5),
  (4, 'executor2', 'executor22@example.com', '356a192b7913b04c54574d18c28d46e6395428ab', 2, 6);

ALTER TABLE `user`
ADD PRIMARY KEY (`id`);


ALTER TABLE `user`
MODIFY `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT =5;