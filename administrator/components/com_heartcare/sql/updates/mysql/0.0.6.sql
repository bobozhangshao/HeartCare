CREATE TABLE IF NOT EXISTS `#__health_device`(
  `id`                    INT(11)      NOT NULL AUTO_INCREMENT,
  `device_id`             VARCHAR(25)  NOT NULL ,
  `user_id`               INT(11)      NOT NULL ,
  `device_type`           VARCHAR(25)  NOT NULL ,
  `register_time`         DATETIME ,
  `produce_date`          DATE ,
  `images`                TEXT,
  `sensors`               TEXT,
  `description`           TEXT,
  `service`               TEXT,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__health_data`(
  `id`                    INT(11)      NOT NULL AUTO_INCREMENT,
  `device_id`             VARCHAR(25)  NOT NULL ,
  `user_id`               INT(11)      NOT NULL ,
  `data_type`             VARCHAR(25)  NOT NULL ,
  `measure_time`          DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `diagnosis`             TEXT ,
  `data_route`            TEXT ,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__health_data_category`(
  `id`                    INT(11)      NOT NULL AUTO_INCREMENT,
  `data_type`             VARCHAR(25)  NOT NULL ,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET = utf8;

INSERT INTO `#__health_data_category` (`data_type`) VALUES
  ('ECG'),
  ('ICG'),
  ('deltaZ'),
  ('Z0'),
  ('ACC_X'),
  ('ACC_Y'),
  ('ACC_Z'),
  ('GRRO_X'),
  ('GRRO_Y');