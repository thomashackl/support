<?php
class Content extends DBMigration
{
    function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `supportplugin_links` (
            `id` VARCHAR(32),
            `link` VARCHAR(2048) NOT NULL DEFAULT '',
            `title` VARCHAR(255) NOT NULL DEFAULT '',
            `description` TEXT NOT NULL DEFAULT '',
            `position` INT NOT NULL DEFAULT 1,
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) COLLATE latin1_german1_ci CHARACTER SET latin1");
    }

    function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_links`");
    }

}