<?php
if ($links) {
    $url = $controller->url_for('links/sort');
    $pos = strpos($url, '?');
    if ($pos !== false) {
        $url = substr($url, 0, $pos);
    }
?>
<div id="supportlinks" data-sort-url="'<?= $url ?>'">
    <?php
    $i = 0;
    foreach ($links as $link) {
    ?>
    <div class="supportlink" id="<?= $link->id ?>">
    <?= formatReady('++['.$link->title.']'.$link->link.' ++') ?>
    <?php
        if ($i_am_root) {
    ?>
        <div class="actions">
            <a href="<?= $controller->url_for('links/edit', $link->id) ?>" data-dialog="size=auto" title="<?= dgettext('supportplugin', 'Eintrag bearbeiten') ?>">
                <?= Assets::img('icons/blue/edit.svg') ?></a>
            <a href="<?= $controller->url_for('links/delete', $link->id) ?>" title="<?= dgettext('supportplugin', 'Eintrag löschen') ?>">
                <?= Assets::img('icons/blue/trash.svg') ?></a>
            <?php if ($i > 0) { ?>
            <a href="<?= $controller->url_for('links/down', $link->id) ?>" class="hidden-js" title="<?= dgettext('supportplugin', 'Eintrag hochsortieren') ?>">
                <?= Assets::img('icons/blue/arr_1up.svg') ?></a>
            <?php } ?>
            <?php if ($i < sizeof($links)-1) { ?>
            <a href="<?= $controller->url_for('links/up', $link->id) ?>" class="hidden-js" title="<?= dgettext('supportplugin', 'Eintrag runtersortieren') ?>">
                <?= Assets::img('icons/blue/arr_1down.svg') ?></a>
            <?php } ?>
        </div>
    <?php
            $i++;
        }
    ?>
        <br/>
        <?= formatReady($link->description) ?>
    </div>
    <?php } ?>
</div>
<?php } else { ?>
<i><?= dgettext('supportplugin', 'Es sind keine Links vorhanden.') ?></i>
<?php } ?>
<script type="text/javascript">
//<!--
STUDIP.SupportPlugin.init();
//-->
</script>
<?php
