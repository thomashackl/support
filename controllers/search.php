<?php

/**
 * SearchController
 *
 * Provides global search functions for support members.
 *
 * @author  Thomas Hackl <thomas.hackl@uni-passau.de>
 * @version 1.0
 */

class SearchController extends StudipController {

    public function before_filter(&$action, &$args) {
        $this->current_action = $action;
        $this->validate_args($args);
        $this->flash = Trails_Flash::instance();
        if (Request::isXhr()) {
            $this->set_layout(null);
        } else {
            $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        }
        Navigation::activateItem('/support/search');
        $this->search = QuickSearch::get('searchterm', new SupportSearch())
                    ->setAttributes(array(
                        'placeholder' => dgettext('supportplugin', 'Suchen Sie hier nach Veranstaltungen, Personen und Einrichtungen')
                    ))
                    ->withButton(array('width' => '500'))
                    ->noSelectbox()
                    ->fireJSFunctionOnSelect('STUDIP.SupportPlugin.selectSearchResult');
        if (Request::get('searchterm_parameter')) {
            $this->search->defaultValue(Request::option('searchterm'), Request::get('searchterm_parameter'));
        }
        $this->search = $this->search->render();
        $this->set_content_type('text/html;charset=windows-1252');
        $this->setInfoBoxImage($this->dispatcher->plugin->getPluginURL().'/assets/images/infobox.png');
        $this->addToInfobox(dgettext('supportplugin', 'Informationen'),
                            dgettext('supportplugin', 'Die Suche findet sowohl '.
                            'Personen als auch Veranstaltungen und '.
                            'Einrichtungen, unabhängig von den '.
                            'Sichtbarkeitseinstellungen.'),
                            'icons/16/black/search.png');
    }

    public function index_action() {
        $current_semester = Semester::findCurrent();
        if (time() >= $current_semester->vorles_ende) {
            $this->selected_semester = Semester::findNext()->id;
        } else {
            $this->selected_semester = $current_semester->id;
        }
    }

    public function result_action() {
        CSRFProtection::verifyUnsafeRequest();
        $this->selected_semester = Request::option('semester');
        $search = new SupportSearch();
        if (Request::option('searchterm')) {
            $result = $search->getResultsById(Request::option('searchterm'), get_object_type(Request::option('searchterm')));
        } else if (Request::get('searchterm_parameter')) {
            $result = $search->getResults(Request::get('searchterm_parameter'), array('semester' => Request::option('semester')));
        }
        $this->persons = array();
        $this->courses = array();
        $this->institutes = array();
        foreach ($result as $entry) {
            switch (get_object_type($entry[0])) {
                case 'user':
                    $this->persons[] = User::find($entry[0]);
                    break;
                case 'sem':
                    $this->courses[] = Course::find($entry[0]);
                    break;
                case 'inst':
                case 'fak':
                    $this->institutes[] = Institute::find($entry[0]);
                    break;
            }
        }
    }

    function redirect_action($id) {
        switch(get_object_type($id)) {
            case 'user':
                $u = User::find($id);
                $this->redirect(URLHelper::getLink('dispatch.php/profile', array('username' => $u->username)));
                break;
            case 'sem':
                $this->redirect(URLHelper::getLink('seminar_main.php', array('auswahl' => $id)));
                break;
            case 'inst':
            case 'fak':
                $this->redirect(URLHelper::getLink('institut_main.php', array('auswahl' => $id)));
                break;
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
