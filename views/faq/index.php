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
        <h1>
            <?= dgettext('supportplugin', 'Häufig gestellte Fragen') ?>
            <?= $category ? ' - '.htmlReady($category->getTranslationByLanguage($lang)->name) : '' ?>
        </h1>
        <nav>
            <? if ($editor) { ?>
            <a href="<?= $controller->url_for('faq/edit'); ?>" data-dialog="size=auto" title="<?= dgettext('supportplugin', "Frage/Antwort hinzufügen") ?>">
                <?= Icon::create('add', 'clickable'); ?>
            </a>
            <? } ?>
        </nav>
    </header>
    <?php
    foreach ($faqs as $f) {
        $translation = $f->getTranslationByLanguage($lang);
    ?>
    <article class="<?= ContentBoxHelper::classes($f->id) ?>" id="<?= $f->id ?>">
        <header>
            <h1>
                <a href="<?= ContentBoxHelper::href($f->id, array('contentbox_type' => 'news')) ?>">
                    <?= htmlReady($translation->question); ?>
                </a>
            </h1>
            <nav>
                <?php
                    $i = 0;
                    foreach ($f->categories as $c) {
                        $i++;
                        echo '<a href="'.$controller->url_for('faq/index', $c->id).'">'.
                            $c->getTranslationByLanguage($lang)->name.'</a>';
                        if ($i < sizeof($f->categories)) {
                            echo ', ';
                        }
                    }
                ?>
                <?php if ($editor) { ?>
                <a href="<?= $controller->url_for('faq/edit/'.$f->id); ?>" data-dialog="size=auto" title="<?= dgettext('supportplugin', "Frage/Antwort bearbeiten") ?>">
                    <?= Icon::create('edit', 'clickable'); ?>
                </a>
                <a href="<?= $controller->url_for('faq/delete/'.$f->id); ?>" title="<?= dgettext('supportplugin', "Frage/Antwort löschen") ?>" onclick="return window.confirm('<?= dgettext('supportplugin', 'Eintrag wirklich löschen?') ?>');">
                    <?= Icon::create('trash', 'clickable'); ?>
                </a>
                <?php } ?>
            </nav>
        </header>
        <section>
            <?= formatReady($translation->answer); ?>
        </section>
    </article>
    <?php } ?>
</section>
<?php } else { ?>
<section>
    <?php if ($category) { ?>
    <?= MessageBox::info(sprintf(
            dgettext('supportplugin', 'Es sind keine häufig gestellten Fragen in der Kategorie "%s" vorhanden.'),
            $category->getTranslationByLanguage($lang)->name
            )) ?>
    <?php } else { ?>
    <?= MessageBox::info(dgettext('supportplugin', 'Es sind keine häufig gestellten Fragen vorhanden.')) ?>
    <?php } ?>
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
