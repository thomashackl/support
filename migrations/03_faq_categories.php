<?php

require_once(__DIR__.'/../models/SupportFaq.php');

class FaqCategories extends Migration
{
    function up() {
        DBManager::get()->exec("ALTER TABLE `supportplugin_faq` CHANGE `id` `faq_id` VARCHAR(32) NOT NULL");
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `supportplugin_faq_categories` (
            `category_id` VARCHAR(32) NOT NULL,
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`category_id`)
        )");
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `supportplugin_faq_category` (
            `faq_id` VARCHAR(32) NOT NULL REFERENCES `supportplugin_faq`.`faq_id`,
            `category_id` VARCHAR(32) NOT NULL REFERENCES `supportplugin_faq_categories`.`category_id`,
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`faq_id`, `category_id`)
        )");
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `supportplugin_faq_categories_i18n` (
            `translation_id` VARCHAR(32) NOT NULL,
            `category_id` VARCHAR(32) NOT NULL REFERENCES `supportplugin_faq_categories`.`category_id`,
            `lang` VARCHAR(255) NOT NULL,
            `name` VARCHAR(2048) NOT NULL DEFAULT '',
            `mkdate` INT NOT NULL DEFAULT 0,
            `chdate` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`translation_id`),
            UNIQUE (`category_id`, `lang`)
        )");
        SupportFaq::expireTableScheme();
    }

    function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_faq_categories`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_faq_categories_i18n`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `supportplugin_faq_category`");
        DBManager::get()->exec("ALTER TABLE `supportplugin_faq` CHANGE `faq_id` `id` VARCHAR(32) NOT NULL");
        SupportFaq::expireTableScheme();
    }

}
