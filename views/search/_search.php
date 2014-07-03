<?php use Studip\Button; ?>
<form action="<?= $controller->url_for('search/result') ?>" method="post" id="supportsearch" data-redirect-url="<?= $controller->url_for('search/redirect') ?>">
    <?= $search ?>
    <label for="semester">
        <?= dgettext('supportplugin', 'Semester für Veranstaltungen:') ?>
    </label>
    <select name="semester">
        <option value="all"><?= dgettext('supportplugin', 'alle') ?></option>
        <?php foreach (array_reverse(Semester::getAll()) as $semester) { ?>
        <option value="<?= $semester->id ?>"<?= $semester->id==$selected_semester ? ' selected="selected"' : '' ?>><?= htmlReady($semester->name) ?></option>
        <?php } ?>
    </select>
    <?= CSRFProtection::tokenTag() ?>
</form>
