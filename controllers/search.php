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
        Navigation::activateItem('/support');
        $this->set_content_type('text/html;charset=windows-1252');
    }

    public function index_action() {
        Navigation::activateItem('/support/search');
        if ($this->flash['results']) {
            $this->searchresult = $this->flash['results'];
        }
        $this->search = QuickSearch::get('searchterm', new SupportSearch())
                ->setAttributes(array("placeholder" => dgettext('supportplugin', 'Geben Sie hier Ihren Suchbegriff ein')))
                ->render();
        $current_semester = Semester::findCurrent();
        if (time() >= $current_semester->vorles_ende) {
            $this->selected_semester = Semester::findNext()->id;
        } else {
            $this->selected_semester = $current_semester->id;
        }
    }

    public function do_search_action() {
        CSRFProtection::verifyUnsafeRequest();
        $this->flash['results'] = SupportSearch::doFullSearch(Request::get('searchterm'));
        $this->redirect('search/index');
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
