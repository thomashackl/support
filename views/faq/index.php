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
            <?= dgettext('supportplugin', 'H�ufig gestellte Fragen') ?>
            <?= $category ? ' - '.htmlReady($category->getTranslationByLanguage($lang)->name) : '' ?>
        </h1>
        <nav>
            <? if ($editor) { ?>
            <a href="<?= $controller->url_for('faq/edit'); ?>" data-dialog="size=auto" title="<?= dgettext('supportplugin', "Frage/Antwort hinzuf�gen") ?>">
                <?= Assets::img('icons/blue/add.svg'); ?>
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
                    <?= Assets::img('icons/blue/edit.svg'); ?>
                </a>
                <a href="<?= $controller->url_for('faq/delete/'.$f->id); ?>" title="<?= dgettext('supportplugin', "Frage/Antwort l�schen") ?>" onclick="return window.confirm('<?= dgettext('supportplugin', 'Eintrag wirklich l�schen?') ?>');">
                    <?= Assets::img('icons/blue/trash.svg'); ?>
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
            dgettext('supportplugin', 'Es sind keine h�ufig gestellten Fragen in der Kategorie "%s" vorhanden.'),
            $category->getTranslationByLanguage($lang)->name
            )) ?>
    <?php } else { ?>
    <?= MessageBox::info(dgettext('supportplugin', 'Es sind keine h�ufig gestellten Fragen vorhanden.')) ?>
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
