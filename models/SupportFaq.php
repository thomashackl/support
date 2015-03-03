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
     * Gets all FAQs (matching the given search term, if given)
     *
     * @param int $limit show only given number of entries
     * @param string $searchterm only get FAQs containing this search term
     * @param bool $search_in_question search for term in question
     * @param bool $search_in_answer search for term in answer
     * @param string $language search FAQs with the given language
     * @return array
     */
    public static function getFaqs($limit=0, $searchterm='', $search_in_question=false, $search_in_answer=false, $language='de_DE') {
        if ($searchterm) {
            $query = "SELECT f.`id`
                FROM `supportplugin_faq` f
                    JOIN `supportplugin_faq_i18n` i ON (f.`id`=i.`faq_id` AND i.`lang`=:lang)";
            $where = "";
            if ($search_in_question) {
                $where .= " WHERE i.`question` LIKE :searchterm";
            }
            if ($search_in_answer) {
                if ($where) {
                    $where .= " OR ";
                } else {
                    $where .= " WHERE ";
                }
                $where .= "i.`answer` LIKE :searchterm";
            }
            $order = " ORDER BY f.`position`";
            $limit_str = "";
            if ($limit) {
                $limit_str = " LIMIT ".intval($limit);
            }
            $query .= $where.$order.$limit_str;
            $faqs = array();
            foreach (DBManager::get()->fetchFirst($query, array('searchterm' => '%'.$searchterm.'%', 'lang' => $language)) as $id) {
                $faqs[] = SupportFaq::find($id);
            }
            return $faqs;
        } else {
            $limit_str = "";
            if ($limit) {
                $limit_str = " LIMIT ".intval($limit);
            }
            return self::findBySQL("1 ORDER BY `position`".$limit_str);
        }
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
        $default = null;
        foreach ($this->translations as $t) {
            if ($t->lang == $GLOBALS['DEFAULT_LANGUAGE'] && !$strict) {
                $default = $t;
            } else if ($t->lang == $lang) {
                $translation = $t;
                break;
            }
        }
        return $translation ?: $default;
    }

}
