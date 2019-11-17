<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720047 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
UPDATE `countries` SET `name` = 'Исландия' WHERE `id` = '166';
UPDATE `countries` SET `name` = 'Ливан' WHERE `id` = '422';
UPDATE `countries` SET `name` = 'Лихтенштейн' WHERE `id` = '438';
UPDATE `countries` SET `name` = 'Люксембург' WHERE `id` = '442';
UPDATE `countries` SET `name` = 'Мексика' WHERE `id` = '484';
UPDATE `countries` SET `name` = 'Монако' WHERE `id` = '492';
UPDATE `countries` SET `name` = 'Катар' WHERE `id` = '634';
UPDATE `countries` SET `name` = 'Свазиленд' WHERE `id` = '748';
UPDATE `countries` SET `name` = 'Тайвань' WHERE `id` = '158';
UPDATE `countries` SET `name` = 'Объединенная Республика. Танзания' WHERE `id` = '834';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
