#!/bin/sh

set -e

if test "$1" = "-h" -o "$1" = "--help"; then
	echo "Use: $0 [<language>]"
	echo "Run without arguments to update all translation files."
	exit 0
fi

cd "$(readlink -f "$(dirname "$0")/..")"

DOMAIN=(messages)

POT_DIR="$PWD/lang/i18n"
echo "$POT_DIR"
test -d "$POT_DIR"

POT_FILE="$POT_DIR/$DOMAIN.pot"

/usr/bin/xgettext -j \
	--package-name "$DOMAIN" \
	--package-version "1.0" \
	--language=PHP --from-code=UTF-8 --keyword=_ \
	--no-escape --add-location --sort-by-file \
	--add-comments=I18N \
	--output="$POT_FILE" \
        app/Http/Controllers/*.php
/usr/bin/xgettext -j \
	--package-name "$DOMAIN" \
	--package-version "1.0" \
	--language=PHP --from-code=UTF-8 --keyword=_  --keyword=__ \
	--no-escape --add-location --sort-by-file \
	--add-comments=I18N \
	--output="$POT_FILE" \
        lang/blade-translations/static.php \
        lang/blade-translations/actor/static.php \
        lang/blade-translations/auth/static.php \
        lang/blade-translations/category/static.php \
        lang/blade-translations/classifier_type/static.php \
        lang/blade-translations/default_actor/static.php \
        lang/blade-translations/documents/static.php \
        lang/blade-translations/email/static.php \
        lang/blade-translations/eventname/static.php \
        lang/blade-translations/fee/static.php \
        lang/blade-translations/layouts/static.php \
        lang/blade-translations/matter/static.php \
        lang/blade-translations/renewals/static.php \
        lang/blade-translations/report/static.php \
        lang/blade-translations/role/static.php \
        lang/blade-translations/rule/static.php \
        lang/blade-translations/task/static.php \
        lang/blade-translations/template-members/static.php \
        lang/blade-translations/type/static.php \
        lang/blade-translations/user/static.php \
        lang/blade-translations/seeder.php \
        lang/blade-translations/js/js.php \


/bin/sed --in-place --expression="s/charset=CHARSET/charset=UTF-8/" "$POT_FILE"

update_po() {

        echo "Update $1:"
	/usr/bin/msgmerge \
		--update --no-fuzzy-matching \
		--no-escape --add-location --sort-by-file \
		"$1" "$POT_FILE"
}

for l in $(find "$PWD/lang/i18n/" -type f -name '*.po'); do
    echo ${l}
    update_po "${l}"
done
