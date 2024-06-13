#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# only fr language for now
# run from lang directory

import polib
import json
for lang in ("fr", "en"):
    pofile = polib.pofile(f'i18n/{lang}.po', encoding="utf8")
    translations = {}
    translations_js = {}
    for entry in pofile.translated_entries():
        js= True
        main = True
        for occurrence in entry.occurrences:
            fichier, ligne = occurrence
            if "js.php" in fichier and js:
                translations_js[polib.escape(entry.msgid)] = polib.escape(entry.msgstr)
                js = False
            elif main:
                translations[polib.escape(entry.msgid)] = polib.escape(entry.msgstr)
                main = False
    with open(f"../public/lang/{lang}.json", "w") as f:
        json.dump(translations_js, f, indent=2)
    print(f"Wrote public/lang/{lang}.json")
    with open(f"{lang}.json", "w") as f:
        json.dump(translations, f, indent=2)
    print(f"Wrote lang/{lang}.json")
