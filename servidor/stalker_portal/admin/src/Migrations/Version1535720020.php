<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720020 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `settings` (
  `default_template` varchar(255) NOT NULL DEFAULT ''
) DEFAULT CHARSET=utf8;
INSERT INTO `settings` (`default_template`) VALUE ('smart_launcher:magcore-theme-graphite');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `settings`;
--
EOL
);
    }
}
