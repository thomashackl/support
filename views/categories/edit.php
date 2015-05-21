<?php use Studip\Button, Studip\LinkButton; ?>
<form class="studip_form" action="<?= $controller->url_for('categories/save') ?>" method="post">
    <fieldset>
        <legend>
            <?= dgettext('supportplugin', 'Name der Kategorie') ?>
        </legend>
    <?php
    foreach ($GLOBALS['INSTALLED_LANGUAGES'] as $key => $lang) {
        if ($category) {
            $t = $category->getTranslationByLanguage($key, true);
        }
    ?>
        <label class="caption">
            <?= Assets::img('languages/' . $lang['picture']) ?>
            <?= htmlReady($lang['name']) ?>
            <input type="text" name="translation[<?= $key ?>][name]" value="<?= $t ? htmlReady($t->name) : '' ?>" size="75" maxlength="255"/>
        </label>
        <?php if ($t->id) { ?>
            <input type="hidden" name="translation[<?= $key ?>][id]" value="<?= $t->id ?>"/>
        <?php } ?>
    <?php } ?>
    </fieldset>
    <?= CSRFProtection::tokenTag() ?>
    <?php if ($category) { ?>
    <input type="hidden" name="category_id" value="<?= $category->id ?>"/>
    <?php } ?>
    <?= Button::createAccept(dgettext('supportplugin', 'Speichern'), 'submit', array('data-dialog-button' => '1')) ?>
    <?= LinkButton::createCancel(dgettext('supportplugin', 'Abbrechen'), $controller->url_for('links'), array('data-dialog-button' => '1', 'data-dialog' => 'close')) ?>
</form>
