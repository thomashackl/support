<h1><?= dgettext('supportplugin', 'Suchergebnisse') ?></h1>
<?= $this->render_partial('search/_search') ?>
<?php if ($persons) { ?>
<table class="default">
    <caption><?= dgettext('supportplugin', 'Personen') ?></caption>
    <thead>
        <tr>
            <th><?= dgettext('supportplugin', 'Name') ?></th>
            <th><?= dgettext('supportplugin', 'Nutzername') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($persons as $u) { ?>
        <tr data-id="<?= $u->id ?>">
            <td>
                <a href="<?= URLHelper::getLink('dispatch.php/profile', array('username' => $u->username)) ?>" title="<?= dgettext('supportplugin', sprintf(_('Profil von %s'), htmlReady($u->getFullname()))) ?>">
                    <?= Avatar::getAvatar($u->id)->getImageTag(Avatar::SMALL); ?>
                    <?= htmlReady($u->getFullname()) ?>
                </a>
            </td>
            <td>
                <a href="<?= URLHelper::getLink('dispatch.php/profile', array('username' => $u->username)) ?>" title="<?= dgettext('supportplugin', sprintf(_('Profil von %s'), htmlReady($u->getFullname()))) ?>">
                    <?= htmlReady($u->username) ?>
                </a>
            </td>
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
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $c) { ?>
        <tr data-id="<?= $c->id ?>">
            <td><?= htmlReady($c->start_semester->name) ?></td>
            <td>
                <a href="<?= URLHelper::getLink('seminar_main.php', array('auswahl' => $c->id)) ?>" title="<?= dgettext('supportplugin', sprintf(_('Zur Veranstaltung %s'), htmlReady($c->name))) ?>">
                    <?= CourseAvatar::getAvatar($c->id)->getImageTag(Avatar::SMALL); ?>
                    <?= htmlReady($c->veranstaltungsnummer) ?>
                </a>
            </td>
            <td>
                <a href="<?= URLHelper::getLink('seminar_main.php', array('auswahl' => $c->id)) ?>" title="<?= dgettext('supportplugin', sprintf(_('Zur Veranstaltung %s'), htmlReady($c->name))) ?>">
                    <?= htmlReady($c->name) ?>
                </a>
            </td>
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
        </tr>
    </thead>
    <tbody>
        <?php foreach ($institutes as $i) { ?>
        <tr data-id="<?= $i->id ?>">
            <td>
                <a href="<?= URLHelper::getLink('institut_main.php', array('auswahl' => $i->id)) ?>" title="<?= dgettext('supportplugin', sprintf(_('Zur Einrichtung %s'), htmlReady($i->name))) ?>">
                    <?= InstituteAvatar::getAvatar($i->id)->getImageTag(Avatar::SMALL); ?>
                    <?= htmlReady($i->name) ?>
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
