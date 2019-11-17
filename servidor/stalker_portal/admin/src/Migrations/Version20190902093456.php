<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
class Version20190902093456 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->createTable('users_devices_statistic');
        $table->addColumn('id', \Doctrine\DBAL\Types\Type::INTEGER)->setNotnull(true)->setAutoincrement(true)->setComment('Primary key, autoincrement');
        $table->addColumn('user_id', \Doctrine\DBAL\Types\Type::INTEGER)->setNotnull(true)->setComment('ID from table Users, not unique');
        $table->addColumn('mac', \Doctrine\DBAL\Types\Type::STRING)->setLength(64)->setNotnull(true)->setDefault('')->setComment('MAC address of device, from table Users, if exists');
        $table->addColumn('version', \Doctrine\DBAL\Types\Type::STRING)->setLength(64)->setNotnull(true)->setDefault('')->setComment('image_version of STB from table Users, can be empty string for other third party devices');
        $table->addColumn('operator_id', \Doctrine\DBAL\Types\Type::STRING)->setLength(255)->setNotnull(true)->setDefault('')->setComment('Operator ID from config.ini, can be empty string');
        $table->addColumn('platform', \Doctrine\DBAL\Types\Type::STRING)->setLength(32)->setNotnull(true)->setDefault('')->setComment('client_type from table Users (STB, Android, iOS etc.), can be empty string');
        $table->addColumn('model', \Doctrine\DBAL\Types\Type::STRING)->setLength(16)->setNotnull(true)->setDefault('')->setComment('stb_type from table Users (STB, Android, iOS etc.), can be empty string');
        $table->addColumn('license_key', \Doctrine\DBAL\Types\Type::STRING)->setLength(32)->setNotnull(true)->setDefault('')->setComment('Users license key assigned to the current device');
        $table->addColumn('unique_device_hash', \Doctrine\DBAL\Types\Type::STRING)->setLength(32)->setNotnull(true)->setDefault('')->setComment('unique device hash from user devices data');
        $table->addColumn('fingerprint', \Doctrine\DBAL\Types\Type::BLOB)->setNotnull(true)->setDefault('')->setComment('fingerprint from user devices data');
        $table->addColumn('added', \Doctrine\DBAL\Types\Type::INTEGER)->setNotnull(true)->setDefault(0)->setComment('timestamp of date when device was added');
        $table->addUniqueIndex(['user_id', 'unique_device_hash'], 'unique_device_hash_idx');
        $table->setPrimaryKey(['id']);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->hasTable('users_devices_statistic')) {
            $schema->dropTable('users_devices_statistic');
        }
    }
    public function getDescription()
    {
        return 'Add user devices statistics table';
    }
}
