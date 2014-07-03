<?php
if ($flash['success']) {
    echo MessageBox::success($flash['success']);
}
if ($flash['error']) {
    echo MessageBox::error($flash['error']);
}
?>
<?php if ($links) { ?>
<div id="supportlinks" data-sort-url="<?= $controller->url_for('links/sort') ?>">
    <?php foreach ($links as $link) { ?>
    <div class="supportlink" id="<?= $link->id ?>">
    <?= formatReady('++['.$link->title.']'.$link->link.'++') ?>
    <?php if ($i_am_root) { ?>
        <div class="actions">
            <a href="<?= $controller->url_for('links/edit', $link->id) ?>" rel="lightbox" target="_blank" title="<?= dgettext('supportplugin', 'Eintrag bearbeiten') ?>">
                <?= Assets::img('icons/16/blue/edit.png') ?></a>
            <a href="<?= $controller->url_for('links/delete', $link->id) ?>" target="_blank" title="<?= dgettext('supportplugin', 'Eintrag löschen') ?>">
                <?= Assets::img('icons/16/blue/trash.png') ?></a>
            <a href="<?= $controller->url_for('links/up', $link->id) ?>" target="_blank" class="hidden-js" title="<?= dgettext('supportplugin', 'Eintrag hochsortieren') ?>">
                <?= Assets::img('icons/16/blue/arr1_up.png') ?></a>
            <a href="<?= $controller->url_for('links/down', $link->id) ?>" target="_blank" class="hidden-js" title="<?= dgettext('supportplugin', 'Eintrag runtersortieren') ?>">
                <?= Assets::img('icons/16/blue/arr1_down.png') ?></a>
        </div>
    <?php } ?>
        <br/>
        <?= formatReady($link->description) ?>
    </div>
</div>
    <?php } ?>
<?php } else { ?>
<i><?= dgettext('supportplugin', 'Es sind keine Links vorhanden.') ?></i>
<?php } ?>
<script type="text/javascript">
//<!--
STUDIP.SupportPlugin.init();
//-->
</script>
