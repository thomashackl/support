<?php use Studip\Button; ?>
<h1><?= dgettext('supportplugin', 'Suche') ?></h1>
<form class="studip_form" action="<?= $controller->url_for('search/do_search') ?>" method="post">
    <label class="caption" for="searchterm">
        <?= dgettext('supportplugin', 'Suche nach Veranstaltungen, Personen und Einrichtungen:') ?>
    </label>
    <?= $search ?>
    <?= CSRFProtection::tokenTag() ?>
    <?= Button::createAccept(dgettext('supportplugin', 'Suchen'), 'submit') ?>
</form>
