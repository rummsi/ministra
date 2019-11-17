<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720178 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
DELETE `f1`.* FROM `filters` AS `f1`
  LEFT JOIN (SELECT `id` FROM `filters` GROUP BY `method`, `values_set`) AS `f2`
    ON `f1`.`id` = `f2`.`id`
WHERE `f2`.`id` IS NULL;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
