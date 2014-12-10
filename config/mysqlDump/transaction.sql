CREATE TABLE IF NOT EXISTS `transaction` (
  `id`          INT(11)    NOT NULL,
  `accountId`   INT(11)    NOT NULL,
  `orderId`     INT(11)    NOT NULL,
  `type`        INT(11)    NOT NULL,
  `amount`      BIGINT(20) NOT NULL,
  `status`      INT(11)    NOT NULL,
  `createdTime` INT(11)    NOT NULL DEFAULT '0',
  `changedTime` INT(11)    NOT NULL DEFAULT '0'
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  AUTO_INCREMENT =280;


ALTER TABLE `transaction`
ADD PRIMARY KEY (`id`);

ALTER TABLE `transaction`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT =280;