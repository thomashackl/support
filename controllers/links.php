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
        $this->setInfoBoxImage($this->dispatcher->plugin->getPluginURL().'/assets/images/infobox.png');
        $this->addToInfobox(dgettext('supportplugin', 'Informationen'),
                            dgettext('supportplugin', "Hier stehen wichtige ".
                            "Links zu anderen Systemen oder relevanten ".
                            "Stud.IP-Inhalten."),
                            'icons/16/black/info.png');
        if ($GLOBALS['perm']->have_perm('root')) {
            $this->addToInfobox(dgettext('supportplugin', 'Aktionen'),
                                '<a href="'.$this->url_for('links/edit').
                                '" rel="lightbox" title="'.
                                dgettext('supportplugin', "Link hinzufügen").
                                '">'.
                                dgettext('supportplugin', "Link hinzufügen").
                                '</a>',
                                'icons/16/blue/add.png');
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
        CSRFProtection::verifyUnsafeRequest();
        $link = SupportLink::find($id);
        $title = $link->title;
        if ($link->delete()) {
            $this->flash['success'] = dgettext('supportplugin', sprintf('Der Eintrag "%s" wurde gelöscht.', htmlReady($title)));
        } else {
            $this->flash['error'] = dgettext('supportplugin', sprintf('Der Eintrag "%s" konnte nicht gelöscht werden.', htmlReady($title)));
        }
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
