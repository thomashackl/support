<?php

/**
 * FaqController
 *
 * Provides a list of FAQ.
 *
 * @author  Thomas Hackl <thomas.hackl@uni-passau.de>
 * @version 1.0
 */

class FaqController extends StudipController {

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
            $js = $this->plugin->getPluginURL().'/assets/javascripts/supportplugin.js';
        } else {
            $css = $this->plugin->getPluginURL().'/assets/stylesheets/supportplugin.min.css';
            $js = $this->plugin->getPluginURL().'/assets/javascripts/supportplugin.min.js';
        }
        PageLayout::addStylesheet($css);
        if ($this->editor) {
            PageLayout::addScript($js);
        }
        if ($this->editor) {
            Navigation::activateItem('/support/faq/faqs');
        } else {
            Navigation::activateItem('/support/faq');
        }
        $this->set_content_type('text/html;charset=windows-1252');
    }

    public function index_action($category = '') {
        if ($category) {
            $this->category = SupportFaqCategory::find($category);
        }
        $this->faqs = SupportFaq::getFaqs(0, Request::get('search'),
            Request::get('search_question'), Request::get('search_answer'),
            $this->lang, $category);
        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage($this->dispatcher->plugin->getPluginURL().'/assets/images/sidebar-support.png');
        $search = new SearchWidget(URLHelper::getLink('?'));
        $search->addNeedle(dgettext('supportplugin', 'Fragen/Antworten durchsuchen'), 'search', true);
        $search->addFilter(dgettext('supportplugin', 'Frage'), 'search_question');
        $search->addFilter(dgettext('supportplugin', 'Antwort'), 'search_answer');
        $this->sidebar->addWidget($search);
        if ($this->editor) {
            $actions = new ActionsWidget();
            $actions->addLink(
                dgettext('supportplugin', "Frage/Antwort hinzufügen"),
                $this->url_for('faq/edit'),
                'icons/blue/add.svg',
                array('data-dialog' => 'size=auto')
            );
            $this->sidebar->addWidget($actions);
        }
        $categories = new ViewsWidget();
        $categories->setTitle(dgettext('supportplugin', 'Kategorien'));
        $categories->addLink(dgettext('supportplugin', 'Alle Kategorien'),
            $this->url_for('faq'), null)->setActive($category ? false : true);
        foreach (SupportFaqCategory::getAll($this->lang) as $c) {
            $t = $c->getTranslationByLanguage($this->lang);
            $categories->addLink($t->name, $this->url_for('faq/index', $c->id), null)
                ->setActive($c->id == $category);
        }
        $this->sidebar->addWidget($categories);
    }

    public function edit_action($id='') {
        if ($id) {
            $this->faq = SupportFaq::find($id);
        }
        $this->categories = SupportFaqCategory::getAll($this->lang);
        $cats = $this->faq->categories;
        if ($cats) {
            $this->faqcats = $this->faq->categories->pluck('category_id');
        } else {
            $this->faqcats = array();
        }
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', $id ? dgettext('supportplugin', 'Frage/Antwort bearbeiten') : dgettext('supportplugin', 'Frage/Antwort hinzufügen'));
        }
    }

    public function save_action() {
        CSRFProtection::verifyUnsafeRequest();
        if (Request::option('faq_id')) {
            $faq = SupportFaq::find(Request::option('faq_id'));
        } else {
            $faq = new SupportFaq();
            $maxpos = DBManager::get()->fetchFirst("SELECT MAX(`position`) FROM `supportplugin_faq`");
            $maxpos = intval($maxpos[0]);
            $faq->position = $maxpos + 1;
        }
        $translations = array();
        foreach (Request::getArray('translation') as $lang => $translation) {
            if ($translation['question'] && $translation['answer']) {
                if ($translation['id']) {
                    $t = SupportFaqTranslation::find($translation['id']);
                } else {
                    $t = new SupportFaqTranslation();
                }
                $t->faq_id = Request::option('faq_id');
                $t->lang = $lang;
                $t->question = $translation['question'];
                $t->answer = $translation['answer'];
                $translations[] = $t;
            } else if ($translation['id']) {
                $t = SupportFaqTranslation::find($translation['id']);
                $t->delete();
            }
        }
        $faq->translations = SimpleORMapCollection::createFromArray($translations);
        $categories = array();
        foreach (Request::getArray('categories') as $id) {
            $categories[] = SupportFaqCategory::find($id);
        }
        $faq->categories = SimpleORMapCollection::createFromArray($categories);
        if ($faq->store()) {
            PageLayout::postSuccess(dgettext('supportplugin', 'Die Änderungen wurden gespeichert.'));
        } else {
            PageLayout::postError(dgettext('supportplugin', 'Die Änderungen konnten nicht gespeichert werden.'));
        }
        $this->redirect($this->url_for('faq'));
    }

    public function delete_action($id) {
        if ($this->editor) {
            $faq = SupportFaq::find($id);
            if ($faq->delete()) {
                PageLayout::postSuccess(dgettext('supportplugin', 'Der Eintrag wurde gelöscht.'));
            } else {
                PageLayout::postError(dgettext('supportplugin', 'Der Eintrag konnte nicht gelöscht werden.'));
            }
        }
        $this->redirect($this->url_for('faq'));
    }

    public function sort_action() {
        $i = 1;
        foreach (Request::getArray('faq_ids') as $id) {
            $l = SupportFaq::find($id);
            $l->position = $i;
            $l->store();
            $i++;
        }
        $this->redirect($this->url_for('faq'));
    }

    public function up_action($id) {
        $faq = SupportFaq::find($id);
        $next = SupportFaq::findBySQL("`position`>? ORDER BY `position` LIMIT 1", array($faq->position));
        if ($next[0]) {
            $swap = $next[0];
            $swap->position = $faq->position;
            $swap->store();
        }
        $faq->position++;
        $faq->store();
        $this->redirect($this->url_for('faq'));
    }

    public function down_action($id) {
        $faq = SupportLink::find($id);
        $prev = SupportLink::findBySQL("`position`<? ORDER BY `position` DESC LIMIT 1", array($faq->position));
        if ($prev[0]) {
            $swap = $prev[0];
            $swap->position = $faq->position;
            $swap->store();
        }
        $faq->position--;
        $faq->store();
        $this->redirect($this->url_for('faq'));
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
