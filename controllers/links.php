<?php

/**
 * LinksController
 *
 * Provides important links for support members.
 *
 * @author  Thomas Hackl <thomas.hackl@uni-passau.de>
 * @version 1.0
 */

class LinksController extends StudipController {

    public function before_filter(&$action, &$args) {
        $this->current_action = $action;
        $this->validate_args($args);
        $this->flash = Trails_Flash::instance();
        $this->plugin = $this->dispatcher->plugin;
        if (Request::isXhr()) {
            $this->set_layout(null);
        } else {
            $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        }
        Navigation::activateItem('/support/links');
        $this->set_content_type('text/html;charset=windows-1252');
    }

    public function index_action() {
        $this->links = SupportLink::findBySQL("1 ORDER BY `position`, `title`");
        if ($GLOBALS['perm']->have_perm('root')) {
            $this->i_am_root = true;
        } else {
            $this->i_am_root = false;
        }
    }

    public function edit_action($id='') {
        if ($id) {
            $this->link = SupportLink::find($id);
        }
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', $id ? dgettext('supportplugin', 'Link bearbeiten') : dgettext('supportplugin', 'Link hinzufügen'));
            $this->response->add_header('X-No-Buttons', 1);
        }
    }

    public function save_action() {
        CSRFProtection::verifyUnsafeRequest();
        if (Request::option('link_id')) {
            $link = SupportLink::find(Request::option('link_id'));
        } else {
            $link = new SupportLink();
        }
        $link->link = Request::get('url');
        $link->title = Request::get('title');
        $link->description = Request::get('description');
        $link->position = intval(Request::get('position'));
        if ($link->store()) {
            $this->flash['success'] = dgettext('supportplugin', 'Die Änderungen wurden gespeichert.');
        } else {
            $this->flash['error'] = dgettext('supportplugin', 'Die Änderungen konnten nicht gespeichert werden.');
        }
        $this->redirect($this->url_for('links'));
    }

    public function delete_action($id) {
        $link = SupportLink::find($id);
        $title = $link->title;
        if ($link->delete()) {
            $this->flash['success'] = dgettext('supportplugin', sprintf('Der Eintrag "%s" wurde gelöscht.', htmlReady($title)));
        } else {
            $this->flash['error'] = dgettext('supportplugin', sprintf('Der Eintrag "%s" konnte nicht gelöscht werden.', htmlReady($title)));
        }
    }

    public function sort_action() {
        $i = 1;
        foreach (Request::getArray('link_ids') as $id) {
            $l = SupportLink::find($id);
            $l->position = $i;
            $l->store();
            $i++;
        }
        $this->redirect($this->url_for('links'));
    }

    public function up_action($id) {
        $link = SupportLink::find($id);
        $next = SupportLink::findBySQL("`position`>? ORDER BY `position` LIMIT 1", array($link->position));
        if ($next[0]) {
            $swap = $next[0];
            $swap->position = $link->position;
            $swap->store();
        }
        $link->position++;
        $link->store();
        $this->redirect($this->url_for('links'));
    }

    public function down_action($id) {
        $link = SupportLink::find($id);
        $prev = SupportLink::findBySQL("`position`<? ORDER BY `position` DESC LIMIT 1", array($link->position));
        if ($prev[0]) {
            $swap = $prev[0];
            $swap->position = $link->position;
            $swap->store();
        }
        $link->position--;
        $link->store();
        $this->redirect($this->url_for('links'));
    }

    // customized #url_for for plugins
    function url_for($to) {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join('/', $args));
    }

}
