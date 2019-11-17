<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720033 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `package_subscribe_log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `set_state` TINYINT NOT NULL,
  `package_id` INT(11) NOT NULL,
  `initiator_id` INT(11) NULL,
  `initiator` SET("admin","user","api") NOT NULL DEFAULT 'api' ,
  `modified` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)) DEFAULT CHARSET = utf8;
INSERT INTO `adm_grp_action_access`
        (`controller_name`, `action_name`,              `is_ajax`,  `description`, `hidden`)
VALUES  ('tariffs',         'subscribe-log',                    0, 'Logs on/off of user\\'s packages ',  0),
        ('tariffs',         'subscribe-log-json',               1, 'Logs on/off of user\\'s packages by page + filters',  0);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE IF EXISTS `package_subscribe_log`;
--
EOL
);
    }
}
