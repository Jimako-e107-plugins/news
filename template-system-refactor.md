# Template System Refactor — news v3

_Spec version: 2026-05-04_

---

## Background

The v2 plugin inherited a template structure that grew organically and was never
rationalised. Several problems have accumulated:

- One `$NEWS_TEMPLATE` variable is shared by two distinct frontend controllers
  (`news.php` and `news_category.php`), mixing list and category keys in a single file.
- `$NEWS_OTHER_TEMPLATE` has a misleading name — "other" evokes the legacy
  `other_news_menu` rendering style, when in reality this template holds extras that
  accompany a single news item view (related items, navigation, comments).
- `news.php` calls `e107::getTemplate('news', 'news')` without a merge flag, so
  theme overrides require copying the entire template file.
- `admin_config.php` line 1413 calls `getTemplate('news', 'news', 'view')` — a key
  that does not exist in `$NEWS_TEMPLATE`. The call silently returns `false` and falls
  through to `getLayouts()`. A bug, not a feature.

---

## Guiding principles

1. **One controller → one template file.** A developer opening any frontend controller
   can immediately tell which template file drives it.
2. **One menu type → one template key.** Themes override only the keys they need;
   `$merge=true` handles the rest.
3. **Variable names describe content, not provenance.** `$NEWS_EXTRAS_TEMPLATE` says
   what is in the file; `$NEWS_OTHER_TEMPLATE` says nothing useful.
4. **`getLayouts()` keys are user-visible layouts.** Sub-components rendered inside a
   layout (nav, related, comments) belong in a separate variable so they never appear
   in admin dropdowns.

---

## New template file map

| Template file | PHP variable | Used by | Keys |
|---|---|---|---|
| `news_list_template.php` | `$NEWS_LIST_TEMPLATE` | `news.php` | `default`, `list`, `2-column`, _(theme-defined)_ |
| `news_category_template.php` | `$NEWS_CATEGORY_TEMPLATE` | `news_category.php` | `default`, _(theme-defined)_ |
| `news_view_template.php` | `$NEWS_VIEW_TEMPLATE` | `news_viewitem.php` | `default`, `videos`, _(theme-defined)_ |
| `news_grid_template.php` | `$NEWS_GRID_TEMPLATE` | `news_class.php::render_newsgrid()` | `col-md-6`, `col-md-4`, `col-md-3`, `col-lg-4`, `media-list` |
| `news_menu_template.php` | `$NEWS_MENU_TEMPLATE` | all `*_menu.php` files | `latest`, `category`, `months`, `other`, `other2`, `carousel`, `archive` |
| `news_extras_template.php` | `$NEWS_EXTRAS_TEMPLATE` | `news_shortcodes.php` | `related`, `nav`, `comments` |

`news_extras_template.php` replaces `news_other_template.php`.
`news_list_template.php` + `news_category_template.php` replace `news_template.php`.

---

## Changes required by file

### Template files — `templates/`

#### Create `templates/news_list_template.php`
- New file. Move from `news_template.php`:
  - `$NEWS_TEMPLATE['list']` → `$NEWS_LIST_TEMPLATE['list']`
  - `$NEWS_TEMPLATE['default']` → `$NEWS_LIST_TEMPLATE['default']`
  - `$NEWS_TEMPLATE['2-column']` → `$NEWS_LIST_TEMPLATE['2-column']`  //deleted
  - `$NEWS_MENU_TEMPLATE['list']` (currently mis-placed in `news_template.php` lines  //not sure about this
    15–16, 63) → move to `news_menu_template.php` under key `'list'`
  - Add `$NEWS_LIST_INFO` array for `getTemplateInfo()` (mirrors pattern from
    `news_view_template.php`)

#### Create `templates/news_category_template.php`
- New file. Move from `news_template.php`:
  - `$NEWS_TEMPLATE['category']` → `$NEWS_CATEGORY_TEMPLATE['default']`
  - Rename key from `'category'` to `'default'` — it is the default layout for the
    category controller, not a variant of the list layout.
  - Add `$NEWS_CATEGORY_INFO` array.

#### Delete `templates/news_template.php`
- All content redistributed to `news_list_template.php` and
  `news_category_template.php`.
- Add backward-compat shim if needed (see BC section below).

#### Rename `templates/news_other_template.php` → `templates/news_extras_template.php`
- Rename PHP variable `$NEWS_OTHER_TEMPLATE` → `$NEWS_EXTRAS_TEMPLATE` throughout.
- Remove the commented-out legacy `$NEWS_TEMPLATE['related']` block at lines 8–10
  (dead code, predates v2).
- No key changes needed — `related`, `nav`, `comments` are correct.

#### Update `templates/news_menu_template.php`
- Add the orphaned `$NEWS_MENU_TEMPLATE['list']` keys (currently in
  `news_template.php` lines 15–16, 63).

---

### Frontend controllers

#### `news.php`
| Line | Current | Change |
|---|---|---|
| L1026 | `e107::getTemplate('news', 'news', 'list')` | `e107::getTemplate('news', 'news_list', 'list', true, true)` |
| L1386 | `e107::getTemplate('news', 'news', 'list')` | `e107::getTemplate('news', 'news_list', 'list', true, true)` |
| L1406 | `e107::getTemplate('news', 'news')` | `e107::getTemplate('news', 'news_list', null, true, true)` |

#### `news_category.php`
| Line | Current | Change |
|---|---|---|
| L106 | `e107::getTemplate('news', 'news', 'category')` | `e107::getTemplate('news', 'news_category', 'default', true, true)` |

#### `news_viewitem.php`
- No `getTemplate` argument changes needed — already calls `('news', 'news_view', $key)`.
- Add `true, true` (merge + theme override) to match convention.

---

### Admin

#### `admin_config.php`
| Line | Current | Change |
|---|---|---|
| L1050 | `getTemplateInfo('news', 'news', null, 'front', true)` | `getTemplateInfo('news', 'news_list', null, 'front', true)` |
| L1413 | `getTemplate('news', 'news', 'view')` — **bug, key does not exist** | Remove this block entirely; `getLayouts()` on L1418 is the correct path |

---

### `ehandlers/news_class.php`

| Line | Current | Change |
|---|---|---|
| L503 | `getTemplate('news', 'news_menu', 'grid')` BC check | Keep BC check; on true log deprecation notice |
| L512 | `getTemplate('news', 'news_grid', $tmpl)` | Add `true, true` for merge + theme override |
| L268 | `$NEWS_TEMPLATE` parameter in `render_newsitem()` | Rename parameter to `$template` (internal only, not public API) |

---

### Shortcodes — `shortcodes/batch/news_shortcodes.php`

| Lines | Current | Change |
|---|---|---|
| L1243 | `getTemplate('news', 'news_other', 'related')` | `getTemplate('news', 'news_extras', 'related', true, true)` |
| L1286 | `getTemplate('news', 'news_other', 'comments')` | `getTemplate('news', 'news_extras', 'comments', true, true)` |
| L1325, L1341 | `getTemplate('news', 'news_other', 'nav')` | `getTemplate('news', 'news_extras', 'nav', true, true)` |

---

### Menu files

All menu `getTemplate` calls need consistent `true, true` (merge + theme override).

| File | Line | Current | Change |
|---|---|---|---|
| `news.php` | L1026, L1386, L1406 | no merge flags | add `true, true` |
| `latestnews_menu.php` | L53 | `true, true` ✓ | no change |
| `news_archive_menu.php` | L32 | `true, true` ✓ | no change |
| `news_carousel_menu.php` | L29 | `true, true` ✓ | no change |
| `news_categories_menu.php` | L35 | `true, true` ✓ | no change |
| `news_months_menu.php` | L110 | `true, true` ✓ | no change |
| `other_news_menu.php` | L58 | `true, true` ✓ | no change |
| `other_news2_menu.php` | L54 | `true, true` ✓ | no change |

---

### `e_menu.php`

| Line | Current | Change |
|---|---|---|
| L41 | `getLayouts('news','news_grid', 'front', ...)` | No change — `news_grid` file name is unchanged |

---

## Backward compatibility

`news_template.php` is the file most likely to be overridden by existing themes.

Recommended approach: keep `news_template.php` as a **shim** that includes both new
files and copies keys into the legacy variable:

```php
<?php
// news_template.php — backward compatibility shim for v2 themes
// Themes should override news_list_template.php or news_category_template.php instead.

if (!defined('e107_INIT')) { exit; }

// Load the real files so $NEWS_LIST_TEMPLATE and $NEWS_CATEGORY_TEMPLATE are populated
require_once __DIR__ . '/news_list_template.php';
require_once __DIR__ . '/news_category_template.php';

// Expose under the legacy variable name so v2 theme overrides still work
$NEWS_TEMPLATE = array_merge($NEWS_LIST_TEMPLATE, ['category' => $NEWS_CATEGORY_TEMPLATE['default']]);
```

Similarly, `news_other_template.php` becomes a shim:

```php
<?php
// news_other_template.php — backward compatibility shim
require_once __DIR__ . '/news_extras_template.php';
$NEWS_OTHER_TEMPLATE = $NEWS_EXTRAS_TEMPLATE;
```

Both shims can be removed in a future major version once themes have had time to
update.

---

## Summary of file operations

| Operation | File |
|---|---|
| **Create** | `templates/news_list_template.php` |
| **Create** | `templates/news_category_template.php` |
| **Rename** | `templates/news_other_template.php` → `templates/news_extras_template.php` |
| **Convert to shim** | `templates/news_template.php` |
| **Convert to shim** | `templates/news_other_template.php` (kept as shim after rename) |
| **Update** | `templates/news_menu_template.php` (add `list` keys) |
| **Update** | `news.php` (3 `getTemplate` calls) |
| **Update** | `news_category.php` (1 `getTemplate` call) |
| **Update** | `news_viewitem.php` (add merge flags) |
| **Update** | `admin_config.php` (fix `getTemplateInfo` arg, remove bug on L1413) |
| **Update** | `ehandlers/news_class.php` (add merge flags, BC deprecation notice) |
| **Update** | `shortcodes/batch/news_shortcodes.php` (4 `getTemplate` calls) |
