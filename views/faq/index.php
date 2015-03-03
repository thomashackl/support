<?php
if ($flash['success']) {
    echo MessageBox::success($flash['success']);
}
if ($flash['error']) {
    echo MessageBox::error($flash['error']);
}
?>
<?php
if ($faqs) {
    $url = $controller->url_for('faq/sort');
    $pos = strpos($url, '?');
    if ($pos !== false) {
        $url = substr($url, 0, $pos);
    }
?>
<section class="contentbox" id="supportfaqs" data-sort-url="'<?= $url ?>'">
    <header>
        <h1><?= dgettext('supportplugin', 'Häufig gestellte Fragen') ?></h1>
        <nav>
            <? if ($editor) { ?>
            <a href="<?= $controller->url_for('faq/edit'); ?>" data-dialog="size=auto;buttons=false" title="<?= dgettext('supportplugin', "Frage/Antwort hinzufügen") ?>">
                <?= Assets::img('icons/16/blue/add.png'); ?>
            </a>
            <? } ?>
        </nav>
    </header>
    <?php
    foreach ($faqs as $f) {
        $translation = $f->getTranslationByLanguage($GLOBALS['user']->id != 'nobody' ? $GLOBALS['user']->preferred_language : $_SESSION['_language']);
    ?>
    <article class="<?= $f->id ?>">
        <header>
            <h1>
                <a href="<?= ContentBoxHelper::href($f->id, array('contentbox_type' => 'news')) ?>">
                    <?= htmlReady($translation->question); ?>
                </a>
            </h1>
            <? if ($editor) { ?>
            <nav>
                <a href="<?= $controller->url_for('faq/edit/'.$f->id); ?>" data-dialog="size=auto" title="<?= dgettext('supportplugin', "Frage/Antwort bearbeiten") ?>">
                    <?= Assets::img('icons/16/blue/edit.png'); ?>
                </a>
                <a href="<?= $controller->url_for('faq/delete/'.$f->id); ?>" title="<?= dgettext('supportplugin', "Frage/Antwort löschen") ?>" onclick="return window.confirm(<?= dgettext('supportplugin', 'Eintrag wirklich löschen?') ?>);">
                    <?= Assets::img('icons/16/blue/trash.png'); ?>
                </a>
            </nav>
            <? } ?>
        </header>
        <section>
            <?= formatReady($translation->answer); ?>
        </section>
    </article>
    <?php } ?>
</section>
<?php } else { ?>
<section>
    <?= dgettext('supportplugin', 'Es sind keine häufig gestellten Fragen vorhanden.') ?>
</section>
<?php
}
if ($editor) {
?>
<script type="text/javascript">
    //<!--
    STUDIP.SupportPlugin.init();
    //-->
</script>
<?php
}
$sidebar = Sidebar::get();
$sidebar->setImage($plugin->getPluginURL().'/assets/images/sidebar-faq.png');
$search = new SearchWidget(URLHelper::getLink('?'));
$search->addNeedle(dgettext('supportplugin', 'Fragen/Antworten durchsuchen'), 'search', true);
$search->addFilter(dgettext('supportplugin', 'Frage'), 'search_question');
$search->addFilter(dgettext('supportplugin', 'Antwort'), 'search_answer');
$sidebar->addWidget($search);
if ($editor) {
    $actions = new ActionsWidget();
    $actions->addLink(
        dgettext('supportplugin', "Frage/Antwort hinzufügen"),
        $controller->url_for('faq/edit'),
        'icons/16/blue/add.png',
        array('data-dialog' => 'size=auto;buttons=false')
    );
    $sidebar->addWidget($actions);
}
