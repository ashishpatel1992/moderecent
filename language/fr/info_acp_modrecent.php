<?php
/**
 *
 * Modérer le contenu récent. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 Ashish Patel <https://ashishpatel.dev>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'ACP_MODRECENT_TITLE'   => 'Modérer le contenu récent',
    'ACP_MODRECENT'         => 'Modérer le contenu récent',
));
