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
        if ($GLOBALS['perm']->have_perm('root') || $support) {
            parent::__construct();
            // Localization
            bindtextdomain('supportplugin', realpath(dirname(__FILE__).'/locale'));
            $navigation = new Navigation($this->getDisplayName(), PluginEngine::getURL($this, array(), 'search'));
            $navigation->setImage($this->getPluginURL().'/assets/images/support.png', array('title' => _('Supportfunktionen'), "@2x" => TRUE));
            $searchNavi = new Navigation(dgettext('supportplugin', 'Suche'), PluginEngine::getURL($this, array(), 'search'));
            $searchNavi->setImage('icons/16/white/search.png');
            $searchNavi->setActiveImage('icons/16/black/search.png');
            $navigation->addSubnavigation('search', $searchNavi);
            $linksNavi = new Navigation(dgettext('supportplugin', 'Wichtige Links'), PluginEngine::getURL($this, array(), 'links'));
            $linksNavi->setImage('icons/16/white/link-intern.png');
            $linksNavi->setActiveImage('icons/16/black/link-intern.png');
            $navigation->addSubnavigation('links', $linksNavi);
            Navigation::addItem('/support', $navigation);
        }
    }

    public function initialize() {
        if (Studip\ENV == 'development') {
            $css = $this->getPluginURL().'/assets/stylesheets/supportplugin.css';
            $js = $this->getPluginURL().'/assets/javascripts/supportplugin.js';
        } else {
            $css = $this->getPluginURL().'/assets/stylesheets/supportplugin.min.css';
            $js = $this->getPluginURL().'/assets/javascripts/supportplugin.min.js';
        }
        PageLayout::addStylesheet($css);
        PageLayout::addScript($js);
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

    private function setupAutoload() {
        StudipAutoloader::addAutoloadPath(realpath(dirname(__FILE__).'/models'));
    }

    public static function onEnable($pluginId) {
        parent::onEnable($pluginId);
        if (!DBManager::get()->fetchAll("SELECT `roleid` FROM `roles` WHERE `rolename`='Support'")) {
            $role = new Role();
            $role->setRolename("Support");
            $rid = RolePersistence::saveRole($role);
            RolePersistence::assignPluginRoles($pluginId, $rid);
        }
    }

    public static function onDisable($pluginId) {
        foreach (RolePersistence::getAssignedPluginRoles($pluginId) as $role) {
            RolePersistence::deleteAssignedPluginRoles($pluginId, $rols);
        }
    }

}
