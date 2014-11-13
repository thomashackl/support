<?php
class Faq extends DBMigration
{
    function up() {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `supportplugin_faq` (
            `id` VARCHAR(32) NOT NULL,
            `position` INT NOT NULL DEFAULT 0,
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) COLLATE latin1_german1_ci CHARACTER SET latin1 ENGINE=InnoDB");
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `supportplugin_faq_i18n` (
            `faq_id` VARCHAR(32) NOT NULL,
            `lang` VARCHAR(255) NOT NULL DEFAULT '',
            `question` VARCHAR(2048) NOT NULL DEFAULT '',
            `answer` VARCHAR(2048) NOT NULL DEFAULT '',
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`faq_id`, `lang`)
        ) COLLATE latin1_german1_ci CHARACTER SET latin1 ENGINE=InnoDB");
    }

    function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_faq`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_faq_i18n`");
    }

}