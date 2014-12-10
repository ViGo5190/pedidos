CREATE TABLE IF NOT EXISTS `order` (
  `id`                             INT(11)      NOT NULL,
  `name`                           VARCHAR(255) NOT NULL,
  `authorId`                       INT(11)      NOT NULL,
  `executorId`                     INT(11)      NOT NULL,
  `describe`                       TEXT         NOT NULL,
  `cost`                           BIGINT(20)   NOT NULL,
  `createdTime`                    INT(11)      NOT NULL,
  `status`                         INT(11)      NOT NULL DEFAULT '1',
  `lastStatusChangedTimeCreation`  INT(11)      NOT NULL,
  `lastStatusChangedTimeExecution` INT(11)      NOT NULL
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  AUTO_INCREMENT =80;


ALTER TABLE `order`
ADD PRIMARY KEY (`id`);


ALTER TABLE `order`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT =80;