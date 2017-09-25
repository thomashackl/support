<?php
class Faq extends Migration
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
            `translation_id` VARCHAR(32) NOT NULL,
            `faq_id` VARCHAR(32) NOT NULL,
            `lang` VARCHAR(255) NOT NULL,
            `question` VARCHAR(2048) NOT NULL DEFAULT '',
            `answer` VARCHAR(2048) NOT NULL DEFAULT '',
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`translation_id`),
            UNIQUE (`faq_id`, `lang`)
        ) COLLATE latin1_german1_ci CHARACTER SET latin1 ENGINE=InnoDB");
        SimpleORMap::expireTableScheme();
    }

    function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_faq`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_faq_i18n`");
        SimpleORMap::expireTableScheme();
    }

}
