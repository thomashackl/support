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

    /**
     * title of the search like "search for courses" or just "courses"
     *
     * @return string
     */
    public function getTitle() {
        return dgettext('supportplugin', 'Suche nach Veranstaltungen, Personen und Einrichtungen');
    }

    /**
     * Returns an URL to a picture of that type. Return "" for nothing found.
     * For example: "return CourseAvatar::getAvatar($id)->getURL(Avatar::SMALL)".
     *
     * @param string $id
     *
     * @return: string URL to a picture
     */
    public function getAvatar($id) {
        switch (get_object_type($id)) {
            case 'sem':
                return CourseAvatar::getAvatar($id)->getURL(Avatar::SMALL);
            case 'user':
                return Avatar::getAvatar($id)->getURL(Avatar::SMALL);
            case 'inst':
            case 'fak':
                return InstituteAvatar::getAvatar($id)->getURL(Avatar::SMALL);
        }
        return "";
    }

    /**
     * Returns an HTML-Tag of a picture of that type. Return "" for nothing found.
     * For example: "return CourseAvatar::getAvatar($id)->getImageTag(Avatar::SMALL)".
     *
     * @param string $id
     *
     * @return string HTML of a picture
     */
    public function getAvatarImageTag($id) {
        switch (get_object_type($id)) {
            case 'sem':
                return CourseAvatar::getAvatar($id)->getImageTag(Avatar::SMALL);
            case 'user':
                return Avatar::getAvatar($id)->getImageTag(Avatar::SMALL);
            case 'inst':
            case 'fak':
                return InstituteAvatar::getAvatar($id)->getImageTag(Avatar::SMALL);
        }
        return "";
    }

    /**
     * Returns the results to a given keyword. To get the results is the
     * job of this routine and it does not even need to come from a database.
     * The results should be an array in the form
     * array (
     *   array($key, $name),
     *   array($key, $name),
     *   ...
     * )
     * where $key is an identifier like user_id and $name is a displayed text
     * that should appear to represent that ID.
     *
     * @param string $keyword
     * @param string $contextual_data
     * @param int $limit maximum number of results (default: all)
     * @param int $offset return results starting from this row (default: 0)
     *
     * @return array
     */
    public function getResults($keyword, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {
        return self::doFullSearch($keyword, $contextual_data, $limit, $offset);
    }

    public function getResultsById($id, $type) {
        $db = DBManager::get();
        switch ($type) {
            case 'user':
                $stmt = $db->prepare("SELECT DISTINCT `user_id`, CONCAT(`Vorname`, ' ', `Nachname`, ' (', `username`, ')') FROM `auth_user_md5` WHERE `user_id`=?");
                break;
            case 'sem':
                if (Config::get()->IMPORTANT_SEMNUMBER) {
                    $stmt = $db->prepare("SELECT DISTINCT `Seminar_id`, IF(`VeranstaltungsNummer`!='', CONCAT(`VeranstaltungsNummer`, ' ', `Name`), `Name`) FROM `seminare` WHERE `Seminar_id`=?");
                } else {
                    $stmt = $db->prepare("SELECT DISTINCT `Seminar_id`, `Name` FROM `seminare` WHERE `Seminar_id`=?");
                }
                break;
            case 'inst':
            case 'fak':
                $stmt = $db->prepare("SELECT DISTINCT `Institut_id`, `Name` FROM `Institute` WHERE `Institut_id`=?");
                break;
        }
        $stmt->execute(array($id));
        return array($stmt->fetch(PDO::FETCH_NUM));
    }

    /**
     * Returns the path to this file, so that this class can be autoloaded and is
     * always available when necessary.
     * Should be: "return __file__;"
     *
     * @return string path to this file
     */
    public function includePath() {
        return __FILE__;
    }

    private function doFullSearch($searchterm, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {
        return array_merge(self::doPersonSearch($searchterm, $contextual_data, $limit, $offset),
            self::doCourseSearch($searchterm, $contextual_data, $limit, $offset),
            self::doInstituteSearch($searchterm, $contextual_data, $limit, $offset));
    }

    private function doPersonSearch($searchterm, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {
        $stmt = DBManager::get()->prepare(
            "SELECT DISTINCT `user_id`, CONCAT(`Vorname`, ' ', `Nachname`, ' (', `username`, ')')
            FROM `auth_user_md5`
            WHERE (`username` LIKE :searchterm
                    OR `Vorname` LIKE :searchterm
                    OR `Nachname` LIKE :searchterm
                    OR CONCAT(`Vorname`, ' ', `Nachname`) LIKE :searchterm
                    OR CONCAT(`Nachname`, ' ', `Vorname`) LIKE :searchterm)
                AND `visible` != 'never'
            ORDER BY `Nachname`, `Vorname`, `username`");
        $stmt->execute(array('searchterm' => implode('%', explode(' ', '%'.$searchterm.'%'))));
        return $stmt->fetchAll(PDO::FETCH_NUM);
    }

    private function doCourseSearch($searchterm, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {
        $parameters = array();
        if (Config::get()->IMPORTANT_SEMNUMBER) {
            $query = "SELECT DISTINCT `Seminar_id`, IF(`VeranstaltungsNummer`!='', CONCAT(`VeranstaltungsNummer`, ' ', `Name`), `Name`)
                FROM `seminare`
                WHERE (`Name` LIKE :searchterm
                    OR `VeranstaltungsNummer` LIKE :searchterm
                    OR `Untertitel` LIKE :searchterm)";
            if ($contextual_data['semester']) {
                if ($contextual_data['semester']) {
                    $semester = Semester::find($contextual_data['semester']);
                    $query .= " AND ((`start_time`+`duration_time` BETWEEN :start AND :end) OR (`start_time`<= :start AND `duration_time`=-1))";
                    $parameters['start'] = $semester->beginn;
                    $parameters['end'] = $semester->ende;
                }
            }
            $query .= " ORDER BY `start_time` DESC, `VeranstaltungsNummer`, `Name`";
        } else {
            $query = "SELECT DISTINCT `Seminar_id`, `Name`
                FROM `seminare`
                WHERE (`Name` LIKE :searchterm
                    OR `VeranstaltungsNummer` LIKE :searchterm)";
            if ($contextual_data['semester']) {
                if ($contextual_data['semester']) {
                    $semester = Semester::find($contextual_data['semester']);
                    $query .= " AND ((`start_time`+`duration_time` BETWEEN :start AND :end) OR (`start_time`<= :start AND `duration_time`=-1))";
                    $parameters['start'] = $semester->beginn;
                    $parameters['end'] = $semester->ende;
                }
            }
            $query .= " ORDER BY `start_time` DESC, `Name`";
        }
        $parameters['searchterm'] = '%'.$searchterm.'%';
        $stmt = DBManager::get()->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetchAll(PDO::FETCH_NUM);
    }

    private function doInstituteSearch($searchterm, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {
        $stmt = DBManager::get()->prepare(
            "SELECT DISTINCT `Institut_id`, `Name`
            FROM `Institute`
            WHERE `Name` LIKE :searchterm
            ORDER BY `Name`");
        $stmt->execute(array('searchterm' => '%'.$searchterm.'%'));
        return $stmt->fetchAll(PDO::FETCH_NUM);
    }

}

