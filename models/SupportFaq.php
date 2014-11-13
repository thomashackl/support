<?php

/**
 * SupportFaq.php
 * model class for table supportplugin_faq
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @copyright   2014 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
class SupportFaq extends SimpleORMap {

    public static function configure($config=array()) {
        $config['db_table'] = 'supportplugin_faq';
        $config['has_many']['translations'] = array(
            'class_name' => 'SupportFaqTranslation',
            'assoc_foreign_key' => 'faq_id',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        parent::configure($config);
    }

    /**
     * Fetches the FAQ translation for the given language. If no translation
     * for the given language is found, the values for the system default
     * language are returned, or null if the $strict parameter is set.
     *
     * @param  $lang language code to fetch
     * @param  bool $strict return
     * @return SupportFaqTranslation Translation object for the given
     *         language, or null
     */
    public function getTranslationByLanguage($lang, $strict = false) {
        $translation = null;
        foreach ($this->translations as $t) {
            if ($t->lang == $GLOBALS['DEFAULT_LANGUAGE']) {
                $default = $t;
            } else if ($t->lang == $lang) {
                $translation = $t;
                break;
            }
        }
        return $translation ?: $default;
    }

}
