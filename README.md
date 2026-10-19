# Moderate Recent

An administrator tool for phpBB 3.3 that lists the **latest topics and posts** in the Administration Control Panel and lets you clean up spam quickly: delete a topic or post, delete **all posts by an author**, or delete the **author's account** together with their posts. Rows can be selected with checkboxes and processed in bulk from a dropdown.

> **Deletions are permanent.** Topics, posts and users are removed with phpBB's own deletion routines (no soft-delete). Always keep a recent database backup.

## Features

- New ACP page: **Extensions → Moderate Recent → Moderate recent content**
- Two lists — recent topics and recent posts — for the last 1–365 days **or an explicit From/To date range**, 25–200 rows each
- Shows title, forum, author, IP address, date and (for posts) a short excerpt
- Per-row actions:
  - Delete topic / Delete post
  - Delete all posts by the author (the account is kept)
  - Delete the author and all of their posts
- Checkbox selection with a **select all** box and a bulk-action dropdown:
  - Delete selected topics / posts
  - Delete all posts by the authors of the selected rows
  - Delete the authors of the selected rows (and their posts)
- A confirmation screen with a summary before anything is deleted
- Every deletion is written to the phpBB moderator log (content) or administrator log (users)
- Safeguards: the current user, guests, bots, founders, administrators and global moderators can never be targeted
- Translations: English, French

## Requirements

- phpBB **3.3.0** or newer (3.3.x; not compatible with 4.x)
- PHP **7.4** or newer

Developed and tested on phpBB 3.3.19 with PHP 8.3.

## Installation

1. Download the extension and copy its contents to `ext/ashishpatel/modrecent/` in your phpBB root, so that `ext/ashishpatel/modrecent/composer.json` exists.
2. In the ACP go to **Customise → Manage extensions**, find **Moderate Recent** and click **Enable**.
3. The page appears under **Extensions → Moderate Recent**.

## Permissions

| Action | Required permission |
|---|---|
| See the page and use the topic/post/user-posts actions | `a_user` (administrator: manage users) |
| Delete users (single or bulk) | `a_userdel` in addition |

Moderators without administrator permissions do not get access to the page.

## Usage

1. Open **Extensions → Moderate Recent → Moderate recent content**.
2. Choose how many days to show — or set a **From** and/or **To** date, which takes priority — and how many rows per list, then click **Apply**.
3. Use the links on a row for a single action, or tick rows and pick an action from **With selected…**, then click **Go**.
4. Review the confirmation message and confirm.

## Updating

Disable the extension, replace the `ext/ashishpatel/modrecent/` files with the new version, then enable it again and purge the cache.

## Uninstalling

**Customise → Manage extensions → Disable → Delete data.** This removes the ACP module. The extension stores no other data, and deleted content is not restored.

## Contributing

Issues and pull requests are welcome. Please keep to the [phpBB coding guidelines](https://area51.phpbb.com/docs/dev/3.3.x/development/coding_guidelines.html).

## Author

**Ashish Patel** — <https://ashishpatel.dev>

## License

[GNU General Public License, version 2](license.txt) (GPL-2.0-only).
