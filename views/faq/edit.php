<form class="default" action="<?= $controller->url_for('faq/save') ?>" method="post">
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
        <?php if ($t->id) { ?>
            <input type="hidden" name="translation[<?= $key ?>][id]" value="<?= $t->id ?>"/>
        <?php } ?>
    </fieldset>
    <?php } ?>
    <?php if ($categories) { ?>
    <fieldset>
        <legend>
            <?= dgettext('supportplugin', 'Kategoriezuordnung') ?>
        </legend>
        <?php foreach ($categories as $c) { ?>
        <label>
            <input type="checkbox" name="categories[]" value="<?= $c->id ?>"<?= in_array($c->id, $faqcats) ? ' checked="checked"' : '' ?>/>
            <?= htmlReady($c->getTranslationByLanguage($lang)->name) ?>
        </label>
        <?php } ?>
    </fieldset>
    <?php } ?>
    <?= CSRFProtection::tokenTag() ?>
    <?php if ($faq) { ?>
    <input type="hidden" name="faq_id" value="<?= $faq->id ?>"/>
    <?php } ?>
    <div data-dialog-button>
        <?= Studip\Button::createAccept(dgettext('supportplugin', 'Speichern'), 'submit') ?>
        <?= Studip\LinkButton::createCancel(dgettext('supportplugin', 'Abbrechen'), $controller->url_for('links'), array('data-dialog' => 'close')) ?>
    </div>
</form>
