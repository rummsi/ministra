<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720060 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('apps')->hasColumn('icons')) {
            $this->addSql('ALTER TABLE `apps` ADD COLUMN `icons` TEXT;');
        }
        $this->addSql(<<<EOL
--

INSERT INTO `apps` (`url`, `added`, `alias`, `name`) VALUES
  ('https://github.com/StalkerApps/vk.music', NOW(), 'vk.music', 'vk.music'),
  ('https://github.com/StalkerApps/exua', NOW(), 'ex.ua', 'ex.ua');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `apps` DROP COLUMN `icons`;
TRUNCATE `apps`;
--
EOL
);
    }
}
