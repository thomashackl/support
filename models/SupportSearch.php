<?php
/**
 * SupportSearch.php - Search functions needed for support work
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 *
 */

class SupportSearch extends SearchType {

    public function getTitle() {
        return dgettext('supportplugin', 'Suche nach Veranstaltungen, Personen und Einrichtungen');
    }

    public function getResults($keyword, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {
         return $this->doPersonSearch($keyword, $contextual_data) +
            $this->doCourseSearch($keyword, $contextual_data) +
            $this->doInstituteSearch($keyword, $contextual_data);
    }

    public function getAvatarImageTag($id) {
        switch(get_object_type($id)) {
            case 'sem':
                $class = 'CourseAvatar';
                break;
            case 'user':
                $class = 'Avatar';
                break;
            case 'inst':
            case 'fak':
                $class = 'InstituteAvatar';
                break;
        }
        if ($class) {
            return $class::getAvatar($id)->getImageTag(Avatar::SMALL);
        } else {
            return '';
        }
    }

    /**
     * Executes a search for a person.
     *
     * @param  String $searchterm the term to search for (parts of or full username/name)
     * @return array  The search results.
     */
    public function doPersonSearch($searchterm, $contextual_data) {
        return DBManager::get()->fetchAll("SELECT DISTINCT a.`user_id`,
                a.`Vorname`, a.`Nachname`, a.`username`, i.`title_front`,
                i.`title_rear`
            FROM `auth_user_md5` a INNER JOIN `user_info` i ON (a.`user_id` = i.`user_id`)
            WHERE (CONCAT(a.`Vorname`, ' ', a.`Nachname`) LIKE :searchterm
                OR CONCAT(a.`Nachname`, ' ', a.`Vorname`) LIKE :searchterm
                OR a.`username` LIKE :searchterm)
            ORDER BY a.`Nachname`, a.`Vorname`, a.`username`",
            array('searchterm' => $searchterm));
    }

    /**
     * Executes a search for a course.
     *
     * @param  String $searchterm the term to search for (parts of or full course name/number)
     * @return array  The search results.
     */
    public function doCourseSearch($searchterm, $contextual_data) {
        $query = "SELECT DISTINCT `Seminar_id`,
                `Name`, `VeranstaltungsNummer`, `status`
            FROM `seminare`
            WHERE `Name` LIKE :searchterm
                OR `VeranstaltungsNummer` LIKE :searchterm";
        if (Config::get()->IMPORTANT_SEMNUMBER) {
            $query = "SELECT DISTINCT `Seminar_id`, IF(`VeranstaltungsNummer`!='', CONCAT(`VeranstaltungsNummer`, ' ', `Name`), `Name`) AS name
                FROM `seminare`
                WHERE `Name` LIKE :searchterm
                    OR `VeranstaltungsNummer` LIKE :searchterm
                ORDER BY name";
        } else {
            $query .= " ORDER BY `Name`, `status`";
        }
        return DBManager::get()->fetchAll($query, array('searchterm' => $searchterm));
    }

    /**
     * Executes a search for an institute.
     *
     * @param  String $searchterm the term to search for (parts of or full name)
     * @return array  The search results.
     */
    public function doInstituteSearch($searchterm, $contextual_data) {
        return DBManager::get()->fetchAll("SELECT DISTINCT `Institut_id`,
                `Name`, `fakultaets_id`
            FROM `Institute`
            WHERE `Name` LIKE :searchterm
            ORDER BY `Name`",
            array('searchterm' => $searchterm));
    }

}
