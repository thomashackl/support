<?php use Studip\Button, Studip\LinkButton; ?>
<form class="studip_form" action="<?= $controller->url_for('faq/save') ?>" method="post">
    <?php
    foreach ($GLOBALS['INSTALLED_LANGUAGES'] as $key => $lang) {
        if ($faq) {
            $t = $faq->getTranslationByLanguage($key, true);
        }
    ?>
    <fieldset>
        <legend>
            <?= Assets::img('languages/' . $lang['picture']) ?>
            <?= htmlReady($lang['name']) ?>
        </legend>
        <label class="caption">
            <?= dgettext('supportplugin', 'Frage') ?>
            <input type="text" name="translation[<?= $key ?>][question]" value="<?= $t ? htmlReady($t->question) : '' ?>" size="75" maxlength="2048"/>
        </label>
        <label class="caption">
            <?= dgettext('supportplugin', 'Antwort') ?>
            <textarea cols="75" rows="4" name="translation[<?= $key ?>][answer]"><?= $t ? htmlReady($t->answer) : '' ?></textarea>
        </label>
        <?php
        if ($t) {
        ?>
        <input type="hidden" name="translation[<?= $key ?>][id]" value="<?= $t->id ?>"/>
        <?php
        }
        ?>
        <input type="hidden" name="translation[<?= $key ?>][lang]" value="<?= $key ?>"/>
    </fieldset>
    <?php } ?>
    <?= CSRFProtection::tokenTag() ?>
    <?php if ($faq) { ?>
    <input type="hidden" name="faq_id" value="<?= $faq->id ?>"/>
    <?php } ?>
    <div class="submit_wrapper">
        <?= Button::createAccept(dgettext('supportplugin', 'Speichern'), 'submit') ?>
        <?= LinkButton::createCancel(dgettext('supportplugin', 'Abbrechen'), $controller->url_for('links')) ?>
    </div>
</form>
