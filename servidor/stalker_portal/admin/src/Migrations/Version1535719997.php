<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719997 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('nginx_secure_link')) {
            $this->addSql('ALTER TABLE `itv` ADD `nginx_secure_link` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `ch_links` ADD `nginx_secure_link` tinyint default 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `nginx_secure_link`;
ALTER TABLE `ch_links` DROP `nginx_secure_link`;
--
EOL
);
    }
}
