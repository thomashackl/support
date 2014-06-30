<h1><?= dgettext('supportplugin', 'Suchergebnisse') ?></h1>
<?= $this->render_partial('search/_search') ?>
<?php if ($persons) { ?>
<table class="default">
    <caption><?= dgettext('supportplugin', 'Personen') ?></caption>
    <thead>
        <tr>
            <th><?= dgettext('supportplugin', 'Name') ?></th>
            <th><?= dgettext('supportplugin', 'Nutzername') ?></th>
            <th><?= dgettext('supportplugin', 'Info') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($persons as $u) { ?>
        <tr data-id="<?= $u->id ?>">
            <td><?= htmlReady($u->getFullname()) ?></td>
            <td><?= htmlReady($u->username) ?></td>
            <td></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
<?php if ($courses) { ?>
<table class="default">
    <caption><?= dgettext('supportplugin', 'Veranstaltungen') ?></caption>
    <thead>
        <tr>
            <th><?= dgettext('supportplugin', 'Semester') ?></th>
            <th><?= dgettext('supportplugin', 'Nummer') ?></th>
            <th><?= dgettext('supportplugin', 'Name') ?></th>
            <th><?= dgettext('supportplugin', 'Info') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $c) { ?>
        <tr data-id="<?= $c->id ?>">
            <td><?= htmlReady($c->start_semester->name) ?></td>
            <td><?= htmlReady($c->veranstaltungsnummer) ?></td>
            <td><?= htmlReady($c->name) ?></td>
            <td></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
<?php if ($institutes) { ?>
<table class="default">
    <caption><?= dgettext('supportplugin', 'Einrichtungen') ?></caption>
    <thead>
        <tr>
            <th><?= dgettext('supportplugin', 'Name') ?></th>
            <th><?= dgettext('supportplugin', 'Info') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($institutes as $i) { ?>
        <tr data-id="<?= $i->id ?>">
            <td><?= htmlReady($i->name) ?></td>
            <td></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
