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
        //echo 'HERE!<br/>';
        // Localization
        bindtextdomain('supportplugin', realpath(dirname(__FILE__).'/locale'));
        $active = $GLOBALS['perm']->have_perm('root') || $support ? 'search' : 'faq';
        $navigation = new Navigation($this->getDisplayName(), PluginEngine::getURL($this, array(), $active));
        $navigation->setImage($this->getPluginURL().'/assets/images/support.svg', array('title' => _('Support')));
        if ($GLOBALS['perm']->have_perm('root') || $support) {
            $searchNavi = new Navigation(dgettext('supportplugin', 'Suche'), PluginEngine::getURL($this, array(), 'search'));
            $searchNavi->setImage('icons/16/white/search.png');
            $searchNavi->setActiveImage('icons/16/black/search.png');
            $navigation->addSubnavigation('search', $searchNavi);
            $linksNavi = new Navigation(dgettext('supportplugin', 'Wichtige Links'), PluginEngine::getURL($this, array(), 'links'));
            $linksNavi->setImage('icons/16/white/link-intern.png');
            $linksNavi->setActiveImage('icons/16/black/link-intern.png');
            $navigation->addSubnavigation('links', $linksNavi);
        }
        $faqNavi = new Navigation(dgettext('supportplugin', 'Häufig gestellte Fragen'), PluginEngine::getURL($this, array(), 'faq'));
        $faqNavi->setImage('icons/16/white/question-circle.png');
        $faqNavi->setActiveImage('icons/16/black/question-circle.png');
        $navigation->addSubnavigation('faq', $faqNavi);
        Navigation::addItem('/support', $navigation);
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

    private function setupAutoload() {
        StudipAutoloader::addAutoloadPath(realpath(dirname(__FILE__).'/models'));
    }

    public static function onEnable($pluginId) {
        parent::onEnable($pluginId);
        // Create "Support" role if necessary and assign plugin.
        if (!DBManager::get()->fetchAll("SELECT `roleid` FROM `roles` WHERE `rolename`='Support'")) {
            $role = new Role();
            $role->setRolename("Support");
            $rid = RolePersistence::saveRole($role);
            RolePersistence::assignPluginRoles($pluginId, array($rid));
        }
        // Assign plugin to "nobody" role.
        RolePersistence::assignPluginRoles($pluginId, array(7));
    }

    public static function onDisable($pluginId) {
        foreach (RolePersistence::getAssignedPluginRoles($pluginId) as $role) {
            RolePersistence::deleteAssignedPluginRoles($pluginId, $rols);
        }
    }

}
