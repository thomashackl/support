<?php
/**
 * Supportlugin.class.php
 *
 * Plugin for functionality needed by support members.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

require 'bootstrap.php';

class SupportPlugin extends StudIPPlugin implements SystemPlugin {

    /**
     * Create a new instance initializing navigation and needed scripts.
     */
    public function __construct() {
        $support = false;
        $roles = RolePersistence::getAssignedRoles($GLOBALS['user']->id);
        foreach ($roles as $role) {
            if ($role->rolename == 'Support') {
                $support = true;
                break;
            }
        }
        parent::__construct();
        // Localization
        bindtextdomain('supportplugin', realpath(dirname(__FILE__).'/locale'));
        $active = $GLOBALS['perm']->have_perm('root') || $support ? 'search' : 'faq';
        $navigation = new Navigation($this->getDisplayName(), PluginEngine::getURL($this, array(), $active));
        $navigation->setImage(Icon::create('rescue', 'navigation', array('title' => _('Support'))));
        if ($GLOBALS['perm']->have_perm('root') || $support) {
            $searchNavi = new Navigation(dgettext('supportplugin', 'Suche'),
                PluginEngine::getURL($this, array(), 'search'));
            $navigation->addSubnavigation('search', $searchNavi);
            $linksNavi = new Navigation(dgettext('supportplugin', 'Wichtige Links'),
                PluginEngine::getURL($this, array(), 'links'));
            $navigation->addSubnavigation('links', $linksNavi);

        }
        $faqNavi = new Navigation(dgettext('supportplugin', 'Häufig gestellte Fragen'),
            PluginEngine::getURL($this, array(), 'faq'));
        if ($GLOBALS['perm']->have_perm('root') || $support) {
            $faqlistNavi = new Navigation(dgettext('supportplugin', 'Häufig gestellte Fragen'),
                PluginEngine::getURL($this, array(), 'faq'));
            $faqNavi->addSubnavigation('faqs', $faqlistNavi);
            $categoriesNavi = new Navigation(dgettext('supportplugin', 'Kategorien'),
                PluginEngine::getURL($this, array(), 'categories'));
            $faqNavi->addSubnavigation('categories', $categoriesNavi);
        }
        $navigation->addSubnavigation('faq', $faqNavi);
        if ($GLOBALS['user']->id !== 'nobody') {
            $ticketNavi = new Navigation(dgettext('supportplugin', 'Supportanfrage stellen'),
                PluginEngine::getURL($this, array(), 'ticket'));
            $navigation->addSubnavigation('ticket', $ticketNavi);
        }
        Navigation::addItem('/support', $navigation);

        // Integrate FAQs in Podium search.
        if (class_exists('Podium')) {
            Podium::addType('faq', dgettext('supportplugin', 'Häufig gestellte Fragen'),
                array($this, 'getPodiumSQL'), array($this, 'getPodiumFilter'));
        }

    }

    /**
     * Plugin name to show in navigation.
     */
    public function getDisplayName() {
        return dgettext('supportplugin', 'Support');
    }

    public function perform($unconsumed_path) {
        $this->setupAutoload();
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'configuration'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    public function getTopFaqs($number) {
        require_once(realpath(dirname(__FILE__).'/models/SupportFaqTranslation.php'));
        require_once(realpath(dirname(__FILE__).'/models/SupportFaq.php'));
        $f = new Flexi_TemplateFactory(realpath(dirname(__FILE__).'/views/faq'));
        $t = $f->open('top');
        $t->set_attribute('language', $GLOBALS['user']->preferred_language ?: $_SESSION['_language']);
        $t->set_attribute('faqs', SupportFaq::getFaqs($number, '', false, false,
            $this->language));
        $t->set_attribute('allfaq', URLHelper::getURL('plugins.php/supportplugin/faq'));
        return $t->render();
    }

    public function getPodiumSQL($search) {

        if (!$search) {
            return null;
        }
        $query = DBManager::get()->quote("%$search%");
        $lang = $GLOBALS['user']->preferred_language ?: Config::get()->DEFAULT_LANGUAGE;
        $sql = "SELECT 'faq' AS type, f.`faq_id` AS id
            FROM `supportplugin_faq` f
                JOIN `supportplugin_faq_i18n` i ON (f.`faq_id`=i.`faq_id` AND i.`lang`='$lang')
            WHERE (i.`question` LIKE $query OR i.`answer` LIKE $query)
                AND i.`lang` = '$lang'";
        return $sql;
    }

    public function getPodiumFilter($faq_id, $search) {
        StudipAutoloader::addAutoloadPath(realpath(__DIR__.'/models'));
        $faq = SupportFaq::find($faq_id);
        $translation = $faq->getTranslationByLanguage($lang);
        $answer = strlen($translation->answer) > 120 ? substr(kill_format($translation->answer), 0, 120).'...' : $translation->answer;
        $result = array(
            'id' => $faq->id,
            'name' => Podium::mark($translation->question, $search),
            'additional' => Podium::mark($answer, $search),
            'url' => URLHelper::getURL('plugins.php/supportplugin/faq#'.$faq->id,
                array(
                    'contentbox_type' => 'news',
                    'contentbox_open' => $faq->id
                )),
            'expand' => URLHelper::getURL('plugins.php/supportplugin/faq',
                array(
                    'search' => $search,
                    'search_question' => 1,
                    'search_answer' => 1
                ))
        );
        return $result;
    }

    private function setupAutoload() {
        StudipAutoloader::addAutoloadPath(realpath(__DIR__.'/models'));
    }

    public static function onEnable($pluginId) {
        parent::onEnable($pluginId);
        // Create "Support" role if necessary and assign plugin.
        if (!$role = DBManager::get()->fetchOne("SELECT `roleid` FROM `roles` WHERE `rolename`='Support'")) {
            $role = new Role();
            $role->setRolename("Support");
            $rid = RolePersistence::saveRole($role);
            RolePersistence::assignPluginRoles($pluginId, array($rid));
        } else {
            RolePersistence::assignPluginRoles($pluginId, array($role['roleid']));
        }
        // Assign plugin to system roles, including "nobody".
        RolePersistence::assignPluginRoles($pluginId, array(1, 2, 3, 4, 5, 6, 7));
    }

    public static function onDisable($pluginId) {
        foreach (RolePersistence::getAssignedPluginRoles($pluginId) as $role) {
            RolePersistence::deleteAssignedPluginRoles($pluginId, $role);
        }
    }

}
