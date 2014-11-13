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
        if (Request::isXhr()) {
            $this->set_layout(null);
        } else {
            $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        }
        Navigation::activateItem('/support/faq');
        $this->set_content_type('text/html;charset=windows-1252');
    }

    public function index_action() {
        $this->faqs = SupportFaq::findBySQL("1 ORDER BY `position`");
        if ($GLOBALS['perm']->have_perm('root')) {
            $this->i_am_root = true;
        } else {
            $this->i_am_root = false;
        }
        $this->setInfoBoxImage($this->dispatcher->plugin->getPluginURL().'/assets/images/infobox.png');
        $this->addToInfobox(dgettext('supportplugin', 'Informationen'),
            dgettext('supportplugin', "H�ufig gestellte Fragen und ihre ".
                "Antworten."), 'icons/16/black/info.png');
        if ($GLOBALS['perm']->have_perm('root')) {
            $this->addToInfobox(dgettext('supportplugin', 'Aktionen'),
                '<a href="'.$this->url_for('faq/edit').
                '" data-dialog="size=auto;buttons=false"  title="'.
                dgettext('supportplugin', "Frage/Antwort hinzuf�gen").
                '">'.
                dgettext('supportplugin', "Frage/Antwort hinzuf�gen").
                '</a>',
                'icons/16/blue/add.png');
        }
    }

    public function edit_action($id='') {
        if ($id) {
            $this->faq = SupportFaq::find($id);
        }
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', $id ? dgettext('supportplugin', 'Frage/Antwort bearbeiten') : dgettext('supportplugin', 'Frage/Antwort hinzuf�gen'));
        }
    }

    public function save_action() {
        CSRFProtection::verifyUnsafeRequest();
        if (Request::option('faq_id')) {
            $faq = SupportFaq::find(Request::option('faq_id'));
        } else {
            $faq = new SupportFaq();
        }
        $maxpos = DBManager::get()->fetchFirst("SELECT MAX(`position`) FROM `supportplugin_faq`");
        $maxpos = intval($maxpos[0]);
        $faq->position = $maxpos+1;
        $faq->translations = array();
        foreach (Request::getArray('translation') as $translation) {
            if ($translation['id']) {
                $t = SupportFaqTranslation::find($translation['id']);
            } else {
                $t = new SupportFaqTranslation();
            }
            $t->lang = $translation['lang'];
            $t->question = $translation['question'];
            $t->answer = $translation['answer'];
            $faq->translations[] = $t;
        }
        if ($faq->store()) {
            $this->flash['success'] = dgettext('supportplugin', 'Die �nderungen wurden gespeichert.');
        } else {
            $this->flash['error'] = dgettext('supportplugin', 'Die �nderungen konnten nicht gespeichert werden.');
        }
        $this->redirect($this->url_for('faq'));
    }

    public function delete_action($id) {
        $faq = SupportFaq::find($id);
        if ($faq->delete()) {
            $this->flash['success'] = dgettext('supportplugin', 'Der Eintrag wurde gel�scht.');
        } else {
            $this->flash['error'] = dgettext('supportplugin', 'Der Eintrag konnte nicht gel�scht werden.');
        }
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
