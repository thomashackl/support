<?php if ($faqs) { ?>
<section class="contentbox">
    <header>
        <h1><?= dgettext('supportplugin', 'Häufig gestellte Fragen') ?></h1>
    </header>
    <?php
    foreach ($faqs as $f) {
        $translation = $f->getTranslationByLanguage($language);
        ?>
        <article class="<?= $f->id ?>">
            <header>
                <h1>
                    <a href="<?= ContentBoxHelper::href($f->id, array('contentbox_type' => 'news')) ?>">
                        <?= htmlReady($translation->question); ?>
                    </a>
                </h1>
            </header>
            <section>
                <?= formatReady($translation->answer); ?>
            </section>
        </article>
    <?php } ?>
</section>
<div style="text-align: center">
    <a href="<?= $allfaq ?>"><?= _('Alle häufig gestellten Fragen anzeigen') ?></a>
</div>
<?php } ?>
