#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# only fr language for now
# run from tools directory

import polib
import json
for lang in ("fr",)
    pofile = polib.pofile(f'../lang/i18n/{lang}.po')
    translations = {}
    for entry in pofile.translated_entries():
        translations[polib.escape(entry.msgid)] = polib.escape(entry.msgstr)
    with open(f"../lang/{lang}.json", "w") as f:
        json.dump(translations, f, indent=2)
