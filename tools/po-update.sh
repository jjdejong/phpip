#!/bin/sh

set -e

if test "$1" = "-h" -o "$1" = "--help"; then
	echo "Use: $0 [<language>]"
	echo "Run without arguments to update all translation files."
	exit 0
fi

cd "$(readlink -f "$(dirname "$0")/..")"

DOMAIN=(messages)

POT_DIR="$PWD/resources/lang/i18n"
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
	--language=PYTHON --from-code=UTF-8 --keyword=_  --keyword=_i \
	--no-escape --add-location --sort-by-file \
	--add-comments=I18N \
	--output="$POT_FILE" \
        resources/views/matter/index.blade.php \
        resources/views/rule/create.blade.php \
        resources/views/rule/index.blade.php \
        resources/views/rule/show.blade.php \
        resources/views/home-js.blade.php \
        resources/views/eventname/create.blade.php \
        resources/views/eventname/index.blade.php \
        resources/views/eventname/show.blade.php \
        resources/views/type/create.blade.php \
        resources/views/type/index.blade.php \
        resources/views/type/show.blade.php \
        resources/views/template-members/create.blade.php \
        resources/views/template-members/index.blade.php \
        resources/views/template-members/show.blade.php \
        resources/views/actor/create.blade.php \
        resources/views/actor/index.blade.php \
        resources/views/actor/show.blade.php \
        resources/views/actor/usedin.blade.php \
        resources/views/matter/show-js.blade.php \
        resources/views/matter/roleActors.blade.php \
        resources/views/matter/createN.blade.php \
        resources/views/matter/edit.blade.php \
        resources/views/matter/create.blade.php \
        resources/views/matter/index.blade.php \
        resources/views/matter/tasks.blade.php \
        resources/views/matter/summary.blade.php \
        resources/views/matter/show.blade.php \
        resources/views/matter/events.blade.php \
        resources/views/matter/classifiers.blade.php \
        resources/views/auth/verify.blade.php \
        resources/views/auth/login.blade.php \
        resources/views/auth/passwords/email.blade.php \
        resources/views/auth/passwords/reset.blade.php \
        resources/views/role/create.blade.php \
        resources/views/role/index.blade.php \
        resources/views/role/show.blade.php \
        resources/views/email/renewalCall.blade.php \
        resources/views/documents/select.blade.php \
        resources/views/documents/create.blade.php \
        resources/views/documents/index.blade.php \
        resources/views/documents/show.blade.php \
        resources/views/documents/select2.blade.php \
        resources/views/classifier_type/create.blade.php \
        resources/views/classifier_type/index.blade.php \
        resources/views/classifier_type/show.blade.php \
        resources/views/category/create.blade.php \
        resources/views/category/index.blade.php \
        resources/views/category/show.blade.php \
        resources/views/report/report1-fr.blade.php \
        resources/views/report/report2-fr.blade.php \
        resources/views/report/report1.blade.php \
        resources/views/renewals/index.blade.php \
        resources/views/renewals/logs.blade.php \
        resources/views/tables/table-js.blade.php \
        resources/views/fee/create.blade.php \
        resources/views/fee/index.blade.php \
        resources/views/layouts/app.blade.php \
        resources/views/vendor/pagination/bootstrap-4.blade.php \
        resources/views/vendor/pagination/simple-bootstrap-4.blade.php \
        resources/views/task/index.blade.php \
        resources/views/default_actor/create.blade.php \
        resources/views/default_actor/index.blade.php \
        resources/views/default_actor/show.blade.php \
        resources/views/welcome.blade.php \
        resources/views/home.blade.php \
        resources/views/user/create.blade.php \
        resources/views/user/index.blade.php \
        resources/views/user/show.blade.php \


/bin/sed --in-place --expression="s/charset=CHARSET/charset=UTF-8/" "$POT_FILE"

update_po() {

        echo "Update $1:"
	/usr/bin/msgmerge \
		--update --no-fuzzy-matching \
		--no-escape --add-location --sort-by-file \
		"$1" "$POT_FILE"
}

for l in $(find "$PWD/resources/lang/i18n/" -type f -name '*.po'); do
    echo ${l}
    update_po "${l}"
done
