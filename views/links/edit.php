<?php use Studip\Button, Studip\LinkButton; ?>
<form class="studip_form" action="<?= $controller->url_for('links/save') ?>" method="post">
    <label class="caption">
        <?= dgettext('supportplugin', 'Linkziel') ?>
        <input type="url" name="url" value="<?= $link ? htmlReady($link->link) : '' ?>" size="75" maxlength="2048"/>
    </label>
    <label class="caption">
        <?= dgettext('supportplugin', 'Titel des Links') ?>
        <input type="text" name="title" value="<?= $link ? htmlReady($link->title) : '' ?>" size="75" maxlength="255"/>
    </label>
    <label class="caption">
        <?= dgettext('supportplugin', 'Beschreibungstext') ?>
        <textarea name="description" cols="75" rows="4"><?= $link ? htmlReady($link->description) : '' ?></textarea>
    </label>
    <label class="caption">
        <?= dgettext('supportplugin', 'Reihenfolge') ?>
        <input type="number" name="position" value="<?= $link ? $link->position : 1 ?>" size="4" maxlength="2" min="1" max="99"/>
    </label>
    <?= CSRFProtection::tokenTag() ?>
    <?php if ($link) { ?>
    <input type="hidden" name="link_id" value="<?= $link->id ?>"/>
    <?php } ?>
    <?= Button::createAccept(dgettext('supportplugin', 'Speichern'), 'submit', array('data-dialog-button' => '1')) ?>
    <?= LinkButton::createCancel(dgettext('supportplugin', 'Abbrechen'), $controller->url_for('links'), array('data-dialog-button' => '1', 'data-dialog' => 'close')) ?>
</form>
