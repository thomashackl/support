<form class="default" action="<?= $controller->url_for('ticket/create') ?>" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>
            <?= dgettext('supportplugin', 'Ihre Anfrage') ?>
        </legend>
        <section>
            <?= dgettext('supportplugin', 'Absender') ?>
            <br>
            <?= htmlReady($GLOBALS['user']->email) ?>
        </section>
        <section>
            <label>
                <span class="required">
                    <?= dgettext('supportplugin', 'Betreff') ?>
                </span>
                <input type="text" name="subject" size="75" maxlength="255"
                       placeholder="<?= dgettext('supportplugin',
                           'Geben Sie hier den Betreff Ihrer Anfrage ein...') ?>"
            </label>
        </section>
        <section>
            <label>
                <span class="required">
                    <?= dgettext('supportplugin', 'Anfrage/Fehlermeldung') ?>
                </span>
                <textarea name="message" cols="75" rows="12" class="wysiwyg"
                          placeholder="<?= dgettext('supportplugin',
                    'Geben Sie hier Ihre Anfrage ein. Bitte beschreiben Sie Fehler möglichst genau, ' .
                    'unter Angabe Ihres Betriebssystems, Browsers und der durchgeführten Aktionen, die zur ' .
                    'Fehlermeldung geführt haben. Beschreiben Sie auch den genauen Text der Fehlermeldung, ' .
                    'am besten, indem Sie einen Screenshot hinzufügen.') ?>"></textarea>
            </label>
        </section>
    </fieldset>
    <fieldset>
        <legend>
            <?= dgettext('supportplugin', 'Datei(en)') ?>
        </legend>
        <section>
            <label class="file-upload">
                <?= dgettext('supportplugin', 'Datei(en) hochladen') ?>
                <input type="file" name="attachments[]" id="attachments" multiple>

                <p class="form-text">
                    <?= dgettext('supportplugin',
                        'Laden Sie hier Screenhots oder weitere Dateien hoch. Mit Strg bzw. Command ' .
                        'können Sie mehrere Dateien auswählen.') ?>
                </p>

                <a onclick="javascript:void 0" class=button><?= dgettext('supportplugin', 'Datei(en) auswählen') ?></a>
            </label>
        </section>
    </fieldset>
    <?= CSRFProtection::tokenTag() ?>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(dgettext('supportplugin', 'Anfrage abschicken'), 'submit') ?>
    </footer>
</form>