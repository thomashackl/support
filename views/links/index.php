<?php
if ($flash['success']) {
    echo MessageBox::success($flash['success']);
}
if ($flash['error']) {
    echo MessageBox::error($flash['error']);
}
?>
<?php if ($links) { ?>
    <?php foreach ($links as $link) { ?>
<div class="supportlink">
    <?= formatReady('++['.$link->title.']'.$link->link.'++') ?>
    <?php if ($i_am_root) { ?>
    <div class="actions">
        <a href="<?= $controller->url_for('links/edit', $link->id) ?>" rel="lightbox" target="_blank" title="<?= dgettext('supportplugin', 'Eintrag bearbeiten') ?>">
            <?= Assets::img('icons/16/blue/edit.png') ?></a>
        <a href="<?= $controller->url_for('links/delete', $link->id) ?>" target="_blank" title="<?= dgettext('supportplugin', 'Eintrag löschen') ?>">
            <?= Assets::img('icons/16/blue/trash.png') ?></a>
    </div>
    <?php } ?>
    <br/>
    <?= formatReady($link->description) ?>
</div>
    <?php } ?>
<?php } else { ?>
<i><?= dgettext('supportplugin', 'Es sind keine Links vorhanden.') ?></i>
<?php } ?>
