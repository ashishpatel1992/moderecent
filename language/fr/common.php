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
    'ACP_MODRECENT_TITLE'           => 'Modérer le contenu récent',
    'ACP_MODRECENT'                 => 'Modérer le contenu récent',
    'MODRECENT_INTRO'               => 'Réservé aux administrateurs. Vérifiez les derniers sujets et messages. Les suppressions sont définitives et sont inscrites dans les journaux des modérateurs et des administrateurs.',
    'MODRECENT_SHOW_LAST'           => 'Afficher les',
    'MODRECENT_DAYS'                => 'derniers jours',
    'MODRECENT_DATE_FROM'           => 'Du',
    'MODRECENT_DATE_TO'             => 'Au',
    'MODRECENT_DATE_HINT'           => 'Les dates ont priorité sur « derniers jours ». Laissez-les vides pour utiliser le nombre de jours.',
    'MODRECENT_LIMIT'               => 'Entrées par liste',
    'MODRECENT_APPLY'               => 'Appliquer',
    'MODRECENT_RECENT_TOPICS'       => 'Sujets récents',
    'MODRECENT_RECENT_POSTS'        => 'Messages récents',
    'MODRECENT_NO_TOPICS'           => 'Aucun sujet pour cette période.',
    'MODRECENT_NO_POSTS'            => 'Aucun message pour cette période.',
    'MODRECENT_COL_TITLE'           => 'Titre',
    'MODRECENT_COL_FORUM'           => 'Forum',
    'MODRECENT_COL_AUTHOR'          => 'Auteur',
    'MODRECENT_COL_IP'              => 'IP',
    'MODRECENT_COL_DATE'            => 'Date',
    'MODRECENT_COL_ACTIONS'         => 'Actions',
    'MODRECENT_DELETE_TOPIC'        => 'Supprimer le sujet',
    'MODRECENT_DELETE_POST'         => 'Supprimer le message',
    'MODRECENT_DELETE_USER_POSTS'   => 'Supprimer tous les messages de l’utilisateur',
    'MODRECENT_DELETE_USER'         => 'Supprimer l’utilisateur',

    'MODRECENT_CONFIRM_DELETE_TOPIC'        => 'Supprimer définitivement le sujet « %s » et toutes ses réponses ?',
    'MODRECENT_CONFIRM_DELETE_POST'         => 'Supprimer définitivement le message « %s » ?',
    'MODRECENT_CONFIRM_DELETE_USER_POSTS'   => 'Supprimer définitivement tous les messages de « %1$s » (%2$d trouvés) ? Le compte est conservé.',
    'MODRECENT_CONFIRM_DELETE_USER'         => 'Supprimer définitivement l’utilisateur « %1$s » et tous ses messages (%2$d trouvés) ? Cette action est irréversible.',

    'MODRECENT_BULK_ACTION'         => 'Avec la sélection…',
    'MODRECENT_BULK_GO'             => 'Exécuter',
    'MODRECENT_SELECT_ALL'          => 'Tout sélectionner',
    'MODRECENT_BULK_DELETE_TOPICS'  => 'Supprimer les sujets sélectionnés',
    'MODRECENT_BULK_DELETE_POSTS'   => 'Supprimer les messages sélectionnés',
    'MODRECENT_BULK_DELETE_USER_POSTS' => 'Supprimer tous les messages des auteurs des lignes sélectionnées',
    'MODRECENT_BULK_DELETE_USERS'   => 'Supprimer les auteurs des lignes sélectionnées (et leurs messages)',
    'MODRECENT_NO_ACTION'           => 'Choisissez d’abord une action.',
    'MODRECENT_NOTHING_SELECTED'    => 'Rien n’a été sélectionné.',
    'MODRECENT_NO_VALID_USERS'      => 'Aucune des lignes sélectionnées n’a d’auteur pouvant être visé (invités, robots, fondateurs, administrateurs, modérateurs et vous-même sont protégés).',
    'MODRECENT_CONFIRM_BULK_TOPICS' => 'Supprimer définitivement les %d sujet(s) sélectionné(s) et toutes leurs réponses ?',
    'MODRECENT_CONFIRM_BULK_POSTS'  => 'Supprimer définitivement les %d message(s) sélectionné(s) ?',
    'MODRECENT_CONFIRM_BULK_USER_POSTS' => 'Supprimer définitivement tous les messages de ces %1$d utilisateur(s) : %2$s ? Les comptes sont conservés.',
    'MODRECENT_CONFIRM_BULK_USERS'  => 'Supprimer définitivement ces %1$d utilisateur(s) et tous leurs messages : %2$s ? Cette action est irréversible.',
    'MODRECENT_BULK_DONE'           => '%d élément(s) supprimé(s).',
    'MODRECENT_BULK_USER_POSTS_DONE'=> '%2$d message(s) de %1$d utilisateur(s) supprimé(s).',
    'MODRECENT_BULK_USERS_DONE'     => '%1$d utilisateur(s) et %2$d de leurs message(s) supprimé(s).',

    'MODRECENT_TOPIC_DELETED'       => 'Le sujet a été supprimé.',
    'MODRECENT_POST_DELETED'        => 'Le message a été supprimé.',
    'MODRECENT_USER_POSTS_DELETED'  => '%1$d message(s) de « %2$s » ont été supprimés.',
    'MODRECENT_USER_DELETED'        => 'L’utilisateur « %s » et ses messages ont été supprimés.',

    'LOG_MODRECENT_TOPIC_DELETED'       => '<strong>Sujet supprimé via Moderate Recent</strong><br />» %s',
    'LOG_MODRECENT_POST_DELETED'        => '<strong>Message supprimé via Moderate Recent</strong><br />» %s',
    'LOG_MODRECENT_USER_POSTS_DELETED'  => '<strong>Tous les messages d’un utilisateur supprimés via Moderate Recent</strong><br />» %1$s (%2$d messages)',
    'LOG_MODRECENT_USER_DELETED'        => '<strong>Utilisateur et ses messages supprimés via Moderate Recent</strong><br />» %s',
));
