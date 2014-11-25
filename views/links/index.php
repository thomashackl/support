<?php
if ($flash['success']) {
    echo MessageBox::success($flash['success']);
}
if ($flash['error']) {
    echo MessageBox::error($flash['error']);
}
?>
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
    <?= formatReady('++['.$link->title.']'.$link->link.'++') ?>
    <?php
        if ($i_am_root) {
    ?>
        <div class="actions">
            <a href="<?= $controller->url_for('links/edit', $link->id) ?>" rel="lightbox" title="<?= dgettext('supportplugin', 'Eintrag bearbeiten') ?>">
                <?= Assets::img('icons/16/blue/edit.png') ?></a>
            <a href="<?= $controller->url_for('links/delete', $link->id) ?>" title="<?= dgettext('supportplugin', 'Eintrag l�schen') ?>">
                <?= Assets::img('icons/16/blue/trash.png') ?></a>
            <?php if ($i > 0) { ?>
            <a href="<?= $controller->url_for('links/down', $link->id) ?>" class="hidden-js" title="<?= dgettext('supportplugin', 'Eintrag hochsortieren') ?>">
                <?= Assets::img('icons/16/blue/arr_1up.png') ?></a>
            <?php } ?>
            <?php if ($i < sizeof($links)-1) { ?>
            <a href="<?= $controller->url_for('links/up', $link->id) ?>" class="hidden-js" title="<?= dgettext('supportplugin', 'Eintrag runtersortieren') ?>">
                <?= Assets::img('icons/16/blue/arr_1down.png') ?></a>
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
$sidebar = Sidebar::get();
$sidebar->setImage($plugin->getPluginURL().'/assets/images/sidebar-links.png');
if ($i_am_root) {
    $actions = new ActionsWidget();
    $actions->addLink(
        dgettext('supportplugin', "Link hinzuf�gen"),
        $controller->url_for('links/edit'),
        'icons/16/blue/add.png',
        array('data-dialog' => 'size=auto;buttons=false')
    );
    $sidebar->addWidget($actions);
}
