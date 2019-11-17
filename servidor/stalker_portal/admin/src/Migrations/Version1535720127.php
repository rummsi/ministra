<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720127 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `support_info` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `lang` ENUM('ru', 'en', 'uk', 'pl', 'el', 'nl', 'it', 'de', 'sk', 'es') NOT NULL,
  `content` TEXT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
INSERT INTO `adm_grp_action_access`
          (`controller_name`,          `action_name`, `is_ajax`, `description`)
VALUES    ('users',                   'support-info',         0, 'Form adding support info'),
          ('users',            'get-support-content',         1, 'Obtaining support info for the specified language'),
          ('users',           'save-support-content',         1, 'Saving support info for the specified language');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `support_info`;
--
EOL
);
    }
}
