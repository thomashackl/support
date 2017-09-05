<form class="default" action="<?= $controller->url_for('links/save') ?>" method="post">
    <section>
        <label class="caption">
            <?= dgettext('supportplugin', 'Linkziel') ?>
            <input type="url" name="url" value="<?= $link ? htmlReady($link->link) : '' ?>" size="75" maxlength="2048"/>
        </label>
    </section>
    <section>
        <label class="caption">
            <?= dgettext('supportplugin', 'Titel des Links') ?>
            <input type="text" name="title" value="<?= $link ? htmlReady($link->title) : '' ?>" size="75" maxlength="255"/>
        </label>
    </section>
    <section>
        <label class="caption">
            <?= dgettext('supportplugin', 'Beschreibungstext') ?>
            <textarea name="description" cols="75" rows="4"><?= $link ? htmlReady($link->description) : '' ?></textarea>
        </label>
    </section>
    <section>
        <label class="caption">
            <?= dgettext('supportplugin', 'Reihenfolge') ?>
            <input type="number" name="position" value="<?= $link ? $link->position : 1 ?>" size="4" min="1" max="99"/>
        </label>
    </section>
    <?= CSRFProtection::tokenTag() ?>
    <?php if ($link) { ?>
    <input type="hidden" name="link_id" value="<?= $link->id ?>"/>
    <?php } ?>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(dgettext('supportplugin', 'Speichern'), 'submit') ?>
        <?= Studip\LinkButton::createCancel(dgettext('supportplugin', 'Abbrechen'), $controller->url_for('links'), array('data-dialog' => 'close')) ?>
    </footer>
</form>
