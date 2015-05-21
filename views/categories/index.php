<?php
if ($flash['success']) {
    echo MessageBox::success($flash['success']);
}
if ($flash['error']) {
    echo MessageBox::error($flash['error']);
}
?>
<?php if ($categories) { ?>
<table class="default">
    <caption><?= dgettext('supportplugin', 'Kategorien häufig gestellter Fragen') ?></caption>
    <thead>
        <tr>
            <th width="80%">
                <?= dgettext('supportplugin', 'Name der Kategorie') ?>
            </th>
            <th width="20%">
                <?= dgettext('supportplugin', 'Aktionen') ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($categories as $c) {
        $translation = $c->getTranslationByLanguage($lang);
    ?>
        <tr class="<?= $f->id ?>">
            <td>
                <?= htmlReady($translation->name) ?>
            </td>
            <? if ($editor) { ?>
            <td width="">
                <a href="<?= $controller->url_for('categories/edit/'.$c->id); ?>" data-dialog="size=auto" title="<?= dgettext('supportplugin', "Kategorie bearbeiten") ?>">
                    <?= Assets::img('icons/16/blue/edit.png'); ?>
                </a>
                <a href="<?= $controller->url_for('categories/delete/'.$c->id); ?>" title="<?= dgettext('supportplugin', "Kategorie löschen") ?>" onclick="return window.confirm('<?= dgettext('supportplugin', 'Kategorie wirklich löschen?') ?>');">
                    <?= Assets::img('icons/16/blue/trash.png'); ?>
                </a>
                <?php } ?>
            </td>
        </article>
        <?php } ?>
        </tr>
    </tbody>
</table>
<?php } else { ?>
    <?= MessageBox::info(dgettext('supportplugin', 'Es sind keine Kategorien für häufig gestellte Fragen vorhanden.')) ?>
<?php } ?>
