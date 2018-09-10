<?php

/**
 * TicketController
 *
 * Write a suppport call, opening a ticket via E-Mail.
 *
 * @author  Thomas Hackl <thomas.hackl@uni-passau.de>
 * @version 1.0
 */

class TicketController extends StudipController {

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
        if (Studip\ENV == 'development') {
            $css = $this->plugin->getPluginURL().'/assets/stylesheets/supportplugin.css';
        } else {
            $css = $this->plugin->getPluginURL().'/assets/stylesheets/supportplugin.min.css';
        }
        PageLayout::addStylesheet($css);
        Navigation::activateItem('/support/ticket');
        $this->sidebar = Sidebar::get();
        $this->sidebar->setImage($this->dispatcher->plugin->getPluginURL().'/assets/images/sidebar-support.png');
    }

    public function index_action() {
        PageLayout::setTitle(
            $this->plugin->getDisplayName() . ' - ' . dgettext('supportplugin', 'Supportanfrage'));
    }

    public function create_action() {
        CSRFProtection::verifyUnsafeRequest();

        $mail = new StudipMail();
        $mail->setSubject(Request::get('subject'))
            ->setSenderEmail($GLOBALS['user']->email)
            ->setSenderName($GLOBALS['user']->getFullname())
            ->setReplyToEmail($GLOBALS['user']->email)
            ->addRecipient($GLOBALS['UNI_CONTACT'])
            ->setBodyHtml(Request::get('message'));

        if ($_FILES['attachments']['name'][0] !== '') {
            $attachments = $_FILES['attachments'];

            foreach ($_FILES['attachments']['name'] as $index => $name) {
                if ($_FILES['attachments']['error'][$index] === 0) {
                    $mail->addFileAttachment($_FILES['attachments']['tmp_name'][$index],
                        $name, $_FILES['attachments']['type'][$index]);
                }
            }
        }

        if ($mail->send()) {
            PageLayout::postSuccess('Ihre Supportanfrage wurde verschickt.');
        } else {
            PageLayout::postError('Ihre Supportanfrage konnte nicht verschickt werden.');
        }

        $this->redirect($this->url_for('ticket'));
    }

    // customized #url_for for plugins
    function url_for($to = '') {
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
