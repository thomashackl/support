<?php

/**
 * SupportFaqCategory.php
 * model class for table supportplugin_faq_categories
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @copyright   2015 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
class SupportFaqCategory extends SimpleORMap {

    public static function configure($config=array()) {
        $config['db_table'] = 'supportplugin_faq_categories';
        $config['has_many']['translations'] = array(
            'class_name' => 'SupportFaqCategoryTranslation',
            'assoc_foreign_key' => 'category_id',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        $config['has_and_belongs_to_many']['faq'] = array(
            'class_name' => 'SupportFaq',
            'thru_table' => 'supportplugin_faq_category',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        parent::configure($config);
    }

    /**
     * Fetches all categories, sorted by their name in the currently set language.
     *
     * @param  $lang language code to fetch
     * @return mixed
     */
    public static function getAll($lang) {
        $all = self::findBySQL("1 ORDER BY `category_id`");
        usort($all, function($a, $b) use ($lang) {
            return strnatcasecmp($a->getTranslationByLanguage($lang)->name,
                $b->getTranslationByLanguage($lang)->name);
        });
        return $all;
    }

    /**
     * Fetches the category translation for the given language. If no translation
     * for the given language is found, the values for the system default
     * language are returned, or null if the $strict parameter is set.
     *
     * @param  $lang language code to fetch
     * @param  bool $strict return
     * @return SupportFaqCategoryTranslation Translation object for the given
     *         language, or null
     */
    public function getTranslationByLanguage($lang, $strict = false) {
        $translation = null;
        $default = null;
        foreach ($this->translations as $t) {
            if ($t->lang == $GLOBALS['DEFAULT_LANGUAGE'] && !$strict) {
                $default = $t;
            }
            if ($t->lang == $lang) {
                $translation = $t;
                break;
            }
        }
        return $translation ?: $default;
    }

}
