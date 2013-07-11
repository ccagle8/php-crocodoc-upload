CREATE TABLE `documents` (
  `auto_id` int(10) NOT NULL AUTO_INCREMENT,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `document` varchar(50) NOT NULL,
  `uuid` varchar(45) NOT NULL,
  `thumbnail` mediumblob NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
