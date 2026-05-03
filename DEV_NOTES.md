# Developer Notes

## Repository structure

This repository mirrors a portion of an e107 installation, not just the plugin folder. The actual plugin lives at `e107_plugins/news/`. Files outside that path (this `DEV_NOTES.md`, `README.md`, `.github/`, etc.) are development-only and never get installed.

```
news-v3-dev/                    # repo root (= e107 installation root)
├── DEV_NOTES.md                # dev only, not installed
├── README.md                   # dev only, not installed
├── LICENSE                     # dev only, not installed
├── .github/                    # dev only, not installed
└── e107_plugins/
    └── news/                   # ← this folder is what gets installed
        ├── plugin.xml
        ├── news.php
        ├── admin_config.php
        ├── e_*.php
        ├── languages/
        ├── shortcodes/
        ├── templates/
        └── ...
```

**Why this layout:** Lite fork's installer can fetch a plugin directly from GitHub by extracting only the `e107_plugins/<plugin_name>/` subfolder. Dev-only files at the repo root (markdown docs, GitHub workflows) stay out of production installations automatically.

When working on this repo:
- Paths in instructions are relative to the **repo root**, which corresponds to an e107 installation root.
- `e107_plugins/news/news.php` is the plugin entry point.
- `e107_plugins/news/languages/English/` is the language folder.
- Plugin code uses standard e107 paths (`e_PLUGIN.'news/...'`, `__DIR__.'/../../class2.php'`) — these work both in repo and after install.

---

## Languages

Plugin uses **e107 2.4 array format** for all language files. Lite fork loads ONLY from `languages/<Language>/` subfolders — root-level files in `languages/` are ignored.

**Lite fork principle:** News is fully standalone. NOTHING related to news may live in core. All news language files must be in this plugin.

### File structure

```
e107_plugins/news/languages/
└── English/
    ├── English_front.php     # Plugin frontend strings (mirror of original e107 2.4 plugin English.php)
    ├── English_admin.php     # Plugin admin strings (mirror of original e107 2.4 plugin English_admin.php)
    ├── English_global.php    # Plugin descriptors LAN_PLUGIN_NEWS_* (mirror of original e107 2.4 plugin English_global.php)
    ├── lan_news.php          # Frontend strings (mirror of original e107 2.4 e107_languages/English/lan_news.php)
    ├── lan_submitnews.php    # Submit news form (mirror of original e107 2.4 e107_languages/English/lan_submitnews.php)
    └── admin/
        └── lan_newspost.php  # Admin/post operations (mirror of original e107 2.4 e107_languages/English/admin/lan_newspost.php)
```

Source files were mirrored from official e107 2.4 core during v3 setup. After that point the plugin owns them — Lite fork core does not contain news strings anymore.

### File format

```php
<?php
return [
    'LAN_KEY' => "Translated text",
];
```

### Loading from PHP code

Use `e107::lan($plugin, $fname, $subfolder)`. Reference verified against `e107_class.php` source (`plugLan()` at line 3920, `lan()` at line 4090).

| Call | Loads |
|---|---|
| `e107::lan('news', false, true)` | `languages/English/English_front.php` |
| `e107::lan('news', true, true)` | `languages/English/English_admin.php` |
| `e107::lan('news', 'global')` | `languages/English/English_global.php` (special case) |
| `e107::lan('news', 'lan_news', true)` | `languages/English/lan_news.php` |
| `e107::lan('news', 'lan_submitnews', true)` | `languages/English/lan_submitnews.php` |
| `e107::lan('news', 'admin/lan_newspost', true)` | `languages/English/admin/lan_newspost.php` |

**Naming gotcha:** the third argument is internally called `$flat` but its meaning is reversed. `$flat = true` actually puts the file INSIDE the `English/` subfolder. `$flat = false` (default) loads from root `languages/`. In Lite fork we always use `true` because root files are not allowed.

**Do NOT use** `e107::coreLan('newspost', true)` or `e107::includeLan(e_LANGUAGEDIR.…)`. Both load from core, which in Lite fork must not contain news strings.

### Plugin descriptor schema

e107 core looks up plugin display names via `e107::getPlugLan('news', '<type>')`, which builds the constant by concatenating `LAN_PLUGIN_NEWS_` + uppercased argument:

| Call | Constant | File |
|---|---|---|
| `e107::getPlugLan('news', 'name')` | `LAN_PLUGIN_NEWS_NAME` | `English_global.php` |
| `e107::getPlugLan('news', 'description')` | `LAN_PLUGIN_NEWS_DESCRIPTION` | `English_global.php` |

Schema is fixed: `LAN_PLUGIN_{FOLDER}_{TYPE}` where `{FOLDER}` is uppercase plugin folder name. Names must match exactly — e107 builds them automatically.

### Sync with e107 2.4 upstream

These files are **initially mirrored from original e107 2.4 core**. When upstream e107 updates the strings, sync periodically. Do not add plugin-specific strings into mirrored files — keep them faithful to upstream so future syncs are clean diffs.

| File in plugin | Upstream source path |
|---|---|
| `e107_plugins/news/languages/English/English_front.php` | `e107_plugins/news/languages/English.php` |
| `e107_plugins/news/languages/English/English_admin.php` | `e107_plugins/news/languages/English_admin.php` |
| `e107_plugins/news/languages/English/English_global.php` | `e107_plugins/news/languages/English_global.php` |
| `e107_plugins/news/languages/English/lan_news.php` | `e107_languages/English/lan_news.php` |
| `e107_plugins/news/languages/English/lan_submitnews.php` | `e107_languages/English/lan_submitnews.php` |
| `e107_plugins/news/languages/English/admin/lan_newspost.php` | `e107_languages/English/admin/lan_newspost.php` |

For new strings unique to v3 features, create a separate file (e.g. `English/lan_news_v3.php`) loaded explicitly.

### Constant naming

Modern e107 2.4 uses descriptive names: `LAN_NEWS_ADMIN_00`, `LAN_PLUGIN_NEWS_NAME`. Legacy numbered names (`NWSLAN_4`, `LAN_NEWS_82`) remain for backward compatibility. New code should use descriptive names.
