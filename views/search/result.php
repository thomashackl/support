<h1><?= dgettext('supportplugin', 'Suchergebnisse') ?></h1>
<?= $this->render_partial('search/_search') ?>
<?php if ($courses) { ?>
<h2><?= dgettext('supportplugin', 'Veranstaltungen') ?></h2>
<?php } ?>
<?php if ($persons) { ?>
<h2><?= dgettext('supportplugin', 'Personen') ?></h2>
<?php } ?>
<?php if ($institutes) { ?>
<h2><?= dgettext('supportplugin', 'Einrichtungen') ?></h2>
<?php } ?>
