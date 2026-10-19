<?php
/**
 *
 * Moderate Recent. An extension for the phpBB Forum Software package.
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
    'ACP_MODRECENT_TITLE'           => 'Moderate Recent',
    'ACP_MODRECENT'                 => 'Moderate recent content',
    'MODRECENT_INTRO'               => 'Administrators only. Review the latest topics and posts. Deletions are permanent and are recorded in the moderator and administrator logs.',
    'MODRECENT_SHOW_LAST'           => 'Show the last',
    'MODRECENT_DAYS'                => 'days',
    'MODRECENT_LIMIT'               => 'Entries per list',
    'MODRECENT_APPLY'               => 'Apply',
    'MODRECENT_RECENT_TOPICS'       => 'Recent topics',
    'MODRECENT_RECENT_POSTS'        => 'Recent posts',
    'MODRECENT_NO_TOPICS'           => 'No topics in this period.',
    'MODRECENT_NO_POSTS'            => 'No posts in this period.',
    'MODRECENT_COL_TITLE'           => 'Title',
    'MODRECENT_COL_FORUM'           => 'Forum',
    'MODRECENT_COL_AUTHOR'          => 'Author',
    'MODRECENT_COL_IP'              => 'IP',
    'MODRECENT_COL_DATE'            => 'Date',
    'MODRECENT_COL_ACTIONS'         => 'Actions',
    'MODRECENT_DELETE_TOPIC'        => 'Delete topic',
    'MODRECENT_DELETE_POST'         => 'Delete post',
    'MODRECENT_DELETE_USER_POSTS'   => 'Delete all posts by user',
    'MODRECENT_DELETE_USER'         => 'Delete user',

    'MODRECENT_CONFIRM_DELETE_TOPIC'        => 'Permanently delete the topic “%s” and all of its replies?',
    'MODRECENT_CONFIRM_DELETE_POST'         => 'Permanently delete the post “%s”?',
    'MODRECENT_CONFIRM_DELETE_USER_POSTS'   => 'Permanently delete all posts by “%1$s” (%2$d found)? The account is kept.',
    'MODRECENT_CONFIRM_DELETE_USER'         => 'Permanently delete the user “%1$s” and all of their posts (%2$d found)? This cannot be undone.',

    'MODRECENT_BULK_ACTION'         => 'With selected…',
    'MODRECENT_BULK_GO'             => 'Go',
    'MODRECENT_SELECT_ALL'          => 'Select all',
    'MODRECENT_BULK_DELETE_TOPICS'  => 'Delete selected topics',
    'MODRECENT_BULK_DELETE_POSTS'   => 'Delete selected posts',
    'MODRECENT_BULK_DELETE_USER_POSTS' => 'Delete all posts by the authors of the selected rows',
    'MODRECENT_BULK_DELETE_USERS'   => 'Delete the authors of the selected rows (and their posts)',
    'MODRECENT_NO_ACTION'           => 'Choose an action first.',
    'MODRECENT_NOTHING_SELECTED'    => 'Nothing was selected.',
    'MODRECENT_NO_VALID_USERS'      => 'None of the selected rows has an author that can be targeted (guests, bots, founders, administrators, moderators and yourself are protected).',
    'MODRECENT_CONFIRM_BULK_TOPICS' => 'Permanently delete the %d selected topic(s) and all of their replies?',
    'MODRECENT_CONFIRM_BULK_POSTS'  => 'Permanently delete the %d selected post(s)?',
    'MODRECENT_CONFIRM_BULK_USER_POSTS' => 'Permanently delete all posts by these %1$d user(s): %2$s? The accounts are kept.',
    'MODRECENT_CONFIRM_BULK_USERS'  => 'Permanently delete these %1$d user(s) and all of their posts: %2$s? This cannot be undone.',
    'MODRECENT_BULK_DONE'           => '%d item(s) have been deleted.',
    'MODRECENT_BULK_USER_POSTS_DONE'=> 'Deleted %2$d post(s) by %1$d user(s).',
    'MODRECENT_BULK_USERS_DONE'     => 'Deleted %1$d user(s) and %2$d of their post(s).',

    'MODRECENT_TOPIC_DELETED'       => 'The topic has been deleted.',
    'MODRECENT_POST_DELETED'        => 'The post has been deleted.',
    'MODRECENT_USER_POSTS_DELETED'  => '%1$d post(s) by “%2$s” have been deleted.',
    'MODRECENT_USER_DELETED'        => 'The user “%s” and their posts have been deleted.',

    'LOG_MODRECENT_TOPIC_DELETED'       => '<strong>Deleted topic via Moderate Recent</strong><br />» %s',
    'LOG_MODRECENT_POST_DELETED'        => '<strong>Deleted post via Moderate Recent</strong><br />» %s',
    'LOG_MODRECENT_USER_POSTS_DELETED'  => '<strong>Deleted all posts of a user via Moderate Recent</strong><br />» %1$s (%2$d posts)',
    'LOG_MODRECENT_USER_DELETED'        => '<strong>Deleted user and their posts via Moderate Recent</strong><br />» %s',
));
