#!/bin/sh

#
#  STEP 1:
#  extract all Stud.IP message strings and merge them with the existing translations
#

LOCALE_RELATIVE_PATH="locale"
TRANSLATIONFILES_RELATIVE_PATHS="."

for language in en
do
    test -f "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po" && mv "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po" "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po.old" > "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po"
    find $TRANSLATIONFILES_RELATIVE_PATHS \( -iname "*.php" \) | xargs xgettext --from-code=UTF-8 -j -n --language=PHP -o "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po"
    test -f "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po.old" && msgmerge "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po.old" "$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po" --output-file="$LOCALE_RELATIVE_PATH/$language/LC_MESSAGES/supportplugin.po"
done
