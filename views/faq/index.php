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
<section class="contentbox" data-sort-url="'<?= $url ?>'">
    <header>
        <h1><?= dgettext('supportplugin', 'H�ufig gestellte Fragen.') ?></h1>
        <nav>
            <? if ($i_am_root) { ?>
            <a href="<?= $controller->url_for('faq/edit'); ?>" data-dialog="size=auto;buttons=false" title="<?= dgettext('supportplugin', "Frage/Antwort hinzuf�gen") ?>">
                <?= Assets::img('icons/16/blue/add.png'); ?>
            </a>
            <? } ?>
        </nav>
    </header>
    <?php
    foreach ($faqs as $f) {
        $translation = $f->getTranslationByLanguage($GLOBALS['user']->preferred_language);
    ?>
    <article class="<?= $f->id ?>">
        <header>
            <h1>
                <a href="<?= ContentBoxHelper::href($f->id, array('contentbox_type' => 'news')) ?>">
                    <?= htmlReady($translation->question); ?>
                </a>
            </h1>
            <? if ($i_am_root) { ?>
            <nav>
                <a href="<?= $controller->url_for('faq/edit/'.$f->id); ?>" data-dialog="size=auto;buttons=false" title="<?= dgettext('supportplugin', "Frage/Antwort bearbeiten") ?>">
                    <?= Assets::img('icons/16/blue/edit.png'); ?>
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
    <?= dgettext('supportplugin', 'Es sind keine h�ufig gestellten Fragen vorhanden.') ?>
</section>
<?php } ?>
<script type="text/javascript">
//<!--
STUDIP.SupportPlugin.init();
//-->
</script>