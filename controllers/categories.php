<?php

/**
 * FaqCategoriesController
 *
 * Provides a list of FAQ.
 *
 * @author  Thomas Hackl <thomas.hackl@uni-passau.de>
 * @version 1.0
 */

class CategoriesController extends StudipController {

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
        if ($GLOBALS['perm']->have_perm('root') || RolePersistence::isAssignedRole($GLOBALS['user']->id, 'Support')) {
            $this->editor = true;
        } else {
            $this->editor = false;
        }
        /*
         * Currently set language:
         * - if user is logged in, use language from user preferences
         * - non logged in users have either set explicitly a different language ($_SESSION)
         * - or use the system default.
         */
        $this->lang = $GLOBALS['user']->id != 'nobody' ?
            $GLOBALS['user']->preferred_language : $_SESSION['_language'] ?:
                $GLOBALS['DEFAULT_LANGUAGE'];
        if (Studip\ENV == 'development') {
            $css = $this->plugin->getPluginURL().'/assets/stylesheets/supportplugin.css';
        } else {
            $css = $this->plugin->getPluginURL().'/assets/stylesheets/supportplugin.min.css';
        }
        PageLayout::addStylesheet($css);
        Navigation::activateItem('/support/faq/categories');
        $this->set_content_type('text/html;charset=windows-1252');
        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage($this->dispatcher->plugin->getPluginURL().'/assets/images/sidebar-support.png');
        if ($this->editor) {
            $actions = new ActionsWidget();
            $actions->addLink(
                dgettext('supportplugin', "Kategorie hinzufügen"),
                $this->url_for('categories/edit'),
                'icons/16/blue/add.png',
                array('data-dialog' => 'size=auto')
            );
            $this->sidebar->addWidget($actions);
        }
    }

    public function index_action() {
        $this->categories = SupportFaqCategory::getAll($this->lang);
    }

    public function edit_action($id='') {
        if ($id) {
            $this->category = SupportFaqCategory::find($id);
        }
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', $id ? dgettext('supportplugin', 'Kategorie bearbeiten') : dgettext('supportplugin', 'Kategorie hinzufügen'));
        }
    }

    public function save_action() {
        CSRFProtection::verifyUnsafeRequest();
        if (Request::option('category_id')) {
            $c = SupportFaqCategory::find(Request::option('category_id'));
        } else {
            $c = new SupportFaqCategory();
        }
        $translations = array();
        foreach (Request::getArray('translation') as $lang => $translation) {
            if ($translation['name']) {
                if ($translation['id']) {
                    $t = SupportFaqCategoryTranslation::find($translation['id']);
                } else {
                    $t = new SupportFaqCategoryTranslation();
                }
                $t->category_id = Request::option('category_id');
                $t->lang = $lang;
                $t->name = $translation['name'];
                $translations[] = $t;
            } else if ($translation['id']) {
                $t = SupportFaqTranslation::find($translation['id']);
                $t->delete();
            }
        }
        $c->translations = SimpleORMapCollection::createFromArray($translations);
        if ($c->store()) {
            $this->flash['success'] = dgettext('supportplugin', 'Die Änderungen wurden gespeichert.');
        } else {
            $this->flash['error'] = dgettext('supportplugin', 'Die Änderungen konnten nicht gespeichert werden.');
        }
        $this->redirect($this->url_for('categories'));
    }

    public function delete_action($id) {
        if ($this->editor) {
            $c = SupportFaqCategory::find($id);
            if ($c->delete()) {
                $this->flash['success'] = dgettext('supportplugin', 'Die Kategorie wurde gelöscht.');
            } else {
                $this->flash['error'] = dgettext('supportplugin', 'Die Kategorie konnte nicht gelöscht werden.');
            }
        }
        $this->redirect($this->url_for('categories'));
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
