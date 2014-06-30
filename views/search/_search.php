<?php use Studip\Button; ?>
<form action="<?= $controller->url_for('search/result') ?>" method="post">
    <?= QuickSearch::get('searchterm', new SupportSearch())
            ->setAttributes(array("placeholder" => dgettext('supportplugin', 'Suchen Sie hier nach Veranstaltungen, Personen und Einrichtungen')))
            ->withButton(array('width' => '500'))
            ->noSelectbox()
            ->render() ?>
    <label for="semester">
        <?= dgettext('supportplugin', 'Semester für Veranstaltungen:') ?>
    </label>
    <select name="semester">
        <option value="all"><?= dgettext('supportplugin', 'alle') ?></option>
        <?php foreach (Semester::getAll() as $semester) { ?>
        <option value="<?= $semester->id ?>"<?= $semester->id==$selected_semester ? ' selected="selected"' : '' ?>><?= htmlReady($semester->name) ?></option>
        <?php } ?>
    </select>
    <?= CSRFProtection::tokenTag() ?>
</form>
