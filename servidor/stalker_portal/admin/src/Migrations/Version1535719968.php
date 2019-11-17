<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719968 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('services_package')->hasColumn('all_services')) {
            $this->addSql('ALTER TABLE `services_package` ADD `all_services` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `users` MODIFY `comment` text;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `services_package` DROP `all_services`;
--
EOL
);
    }
}
