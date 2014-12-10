CREATE TABLE IF NOT EXISTS `account` (
  `id`      INT(11)    NOT NULL,
  `type`    INT(11)    NOT NULL,
  `balance` BIGINT(20) NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  AUTO_INCREMENT =11;


INSERT INTO `account` (`id`, `type`, `balance`) VALUES
  (1, 2, 0),
  (2, 3, 0),
  (3, 1, 100000000),
  (4, 1, 0),
  (5, 1, 100000000),
  (6, 2, 0);

ALTER TABLE `account`
ADD PRIMARY KEY (`id`);


ALTER TABLE `account`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT =11;