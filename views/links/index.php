<?php
if ($flash['success']) {
    echo MessageBox::success($flash['success']);
}
?>
<?php if ($links) { ?>
    <?php foreach ($links as $link) { ?>
<div class="supportlink">
    <?= formatReady('['.$link->title.']'.$link->link) ?></a>
    <a href="<?= $controller->url_for('links/edit', $link->id) ?>" rel="lightbox" target="_blank" title="<?= dgettext('supportplugin', 'Eintrag bearbeiten') ?>"><?= Assets::img('icons/16/blue/edit.png') ?></a>
    <br/>
    <?= formatReady($link->description) ?>
</div>
    <?php } ?>
<?php } else { ?>
<i><?= dgettext('supportplugin', 'Es sind keine Links vorhanden.') ?></i>
<?php } ?>
