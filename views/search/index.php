<?php use Studip\Button; ?>
<h1><?= dgettext('supportplugin', 'Suche') ?></h1>
<form class="studip_form" action="<?= $controller->url_for('search/do_search') ?>" method="post">
    <label class="caption" for="searchterm">
        <?= dgettext('supportplugin', 'Suchbegriff:') ?>
    </label>
    <input type="text" name="searchterm" value="<?= $searchterm ? htmlReady($searchterm) : '' ?>" size="75" maxlength="255" placeholder="<?= dgettext('supportplugin', 'Geben Sie hier Ihren Suchbegriff ein.') ?>"/>    
    <?= CSRFProtection::tokenTag() ?>
    <?= Button::createAccept(dgettext('supportplugin', 'Suchen'), 'submit') ?>
</form>
