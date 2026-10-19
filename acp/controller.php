<?php
/**
 *
 * Moderate Recent. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 Ashish Patel <https://ashishpatel.dev>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ashishpatel\modrecent\acp;

class controller
{
    const DEFAULT_DAYS = 7;
    const DEFAULT_LIMIT = 50;
    const DELETE_CHUNK = 200;

    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\log\log_interface */
    protected $log;

    /** @var \phpbb\request\request_interface */
    protected $request;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var string */
    protected $root_path;

    /** @var string */
    protected $php_ext;

    /** @var string base URL of the ACP module page */
    protected $u_action = '';

    /** @var int[]|null forums the administrator may act on */
    protected $forums;

    public function __construct(
        \phpbb\auth\auth $auth,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\log\log_interface $log,
        \phpbb\request\request_interface $request,
        \phpbb\template\template $template,
        \phpbb\user $user,
        $root_path,
        $php_ext
    )
    {
        $this->auth = $auth;
        $this->db = $db;
        $this->log = $log;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
    }

    public function set_page_url($u_action)
    {
        $this->u_action = $u_action;
    }

    public function handle()
    {
        if (!$this->is_admin() || !$this->get_forums())
        {
            trigger_error('NO_AUTH_OPERATION', E_USER_WARNING);
        }

        $action = $this->request->variable('action', '');

        switch ($action)
        {
            case 'delete_topic':
                $this->delete_topic();
                break;
            case 'delete_post':
                $this->delete_post();
                break;
            case 'delete_user_posts':
                $this->delete_user_posts();
                break;
            case 'delete_user':
                $this->delete_user();
                break;
            case 'bulk':
                $this->bulk();
                break;
        }

        $this->display_lists();
    }

    /**
     * Administrators who may manage users (the same permission that guards the ACP module).
     */
    protected function is_admin()
    {
        return !empty($this->user->data['is_registered']) && $this->auth->acl_get('a_user');
    }

    /**
     * Forums the administrator may act on: every forum except links.
     */
    protected function get_forums()
    {
        if ($this->forums !== null)
        {
            return $this->forums;
        }

        $this->forums = array();

        $sql = 'SELECT forum_id
            FROM ' . FORUMS_TABLE . '
            WHERE forum_type <> ' . FORUM_LINK;
        $result = $this->db->sql_query($sql);
        while ($row = $this->db->sql_fetchrow($result))
        {
            $this->forums[] = (int) $row['forum_id'];
        }
        $this->db->sql_freeresult($result);

        return $this->forums;
    }

    protected function can_delete_users()
    {
        return (bool) $this->auth->acl_get('a_userdel');
    }

    /**
     * A user may only be targeted when they are a plain member: never
     * the current user, a guest, a bot, a founder, an administrator or
     * a global moderator.
     */
    protected function get_target_user($user_id)
    {
        $user_id = (int) $user_id;

        if ($user_id <= 0 || $user_id === ANONYMOUS || $user_id === (int) $this->user->data['user_id'])
        {
            return false;
        }

        $sql = 'SELECT user_id, username, user_colour, user_type
            FROM ' . USERS_TABLE . '
            WHERE user_id = ' . $user_id;
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$row || !in_array((int) $row['user_type'], array(USER_NORMAL, USER_INACTIVE), true))
        {
            return false;
        }

        $sql = 'SELECT ug.user_id
            FROM ' . USER_GROUP_TABLE . ' ug, ' . GROUPS_TABLE . " g
            WHERE ug.user_id = $user_id
                AND ug.user_pending = 0
                AND g.group_id = ug.group_id
                AND " . $this->db->sql_in_set('g.group_name', array('ADMINISTRATORS', 'GLOBAL_MODERATORS'));
        $result = $this->db->sql_query_limit($sql, 1);
        $privileged = (bool) $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        return $privileged ? false : $row;
    }

    protected function count_user_posts($user_id)
    {
        $sql = 'SELECT COUNT(post_id) AS num
            FROM ' . POSTS_TABLE . '
            WHERE poster_id = ' . (int) $user_id . '
                AND ' . $this->db->sql_in_set('forum_id', $this->get_forums());
        $result = $this->db->sql_query($sql);
        $num = (int) $this->db->sql_fetchfield('num');
        $this->db->sql_freeresult($result);

        return $num;
    }

    protected function list_url($extra = array())
    {
        $url = $this->u_action;
        foreach ($extra as $key => $value)
        {
            $url .= '&amp;' . $key . '=' . rawurlencode($value);
        }

        return $url;
    }

    protected function done($message_key, $args = array())
    {
        $message = call_user_func_array(array($this->user, 'lang'), array_merge(array($message_key), $args));

        trigger_error($message . adm_back_link($this->u_action));
    }

    protected function confirm($message, $fields)
    {
        $fields['days'] = $this->request->variable('days', self::DEFAULT_DAYS);
        $fields['limit'] = $this->request->variable('limit', self::DEFAULT_LIMIT);

        confirm_box(false, $message, build_hidden_fields($fields));

        // confirm_box() exits for the "ask" case; reaching here means the prompt was cancelled.
        redirect(htmlspecialchars_decode($this->u_action));
    }

    protected function log_mod($key, $forum_id, $topic_id, $args)
    {
        $this->log->add('mod', $this->user->data['user_id'], $this->user->ip, $key, time(), array_merge(
            array('forum_id' => (int) $forum_id, 'topic_id' => (int) $topic_id),
            $args
        ));
    }

    protected function delete_topic()
    {
        $topic_id = $this->request->variable('t', 0);

        $sql = 'SELECT topic_id, forum_id, topic_title
            FROM ' . TOPICS_TABLE . '
            WHERE topic_id = ' . (int) $topic_id;
        $result = $this->db->sql_query($sql);
        $topic = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$topic || !in_array((int) $topic['forum_id'], $this->get_forums(), true))
        {
            trigger_error('NO_AUTH_OPERATION', E_USER_WARNING);
        }

        if (!confirm_box(true))
        {
            $this->confirm($this->user->lang('MODRECENT_CONFIRM_DELETE_TOPIC', $topic['topic_title']), array(
                'action' => 'delete_topic',
                't' => $topic_id,
            ));
        }

        if (!function_exists('delete_topics'))
        {
            include($this->root_path . 'includes/functions_admin.' . $this->php_ext);
        }

        delete_topics('topic_id', array((int) $topic['topic_id']));
        $this->log_mod('LOG_MODRECENT_TOPIC_DELETED', $topic['forum_id'], 0, array($topic['topic_title']));

        $this->done('MODRECENT_TOPIC_DELETED');
    }

    protected function delete_post()
    {
        $post_id = $this->request->variable('p', 0);

        $sql = 'SELECT p.post_id, p.forum_id, p.post_subject, t.topic_title
            FROM ' . POSTS_TABLE . ' p
            LEFT JOIN ' . TOPICS_TABLE . ' t ON (t.topic_id = p.topic_id)
            WHERE p.post_id = ' . (int) $post_id;
        $result = $this->db->sql_query($sql);
        $post = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$post || !in_array((int) $post['forum_id'], $this->get_forums(), true))
        {
            trigger_error('NO_AUTH_OPERATION', E_USER_WARNING);
        }

        $title = $post['post_subject'] !== '' ? $post['post_subject'] : (string) $post['topic_title'];

        if (!confirm_box(true))
        {
            $this->confirm($this->user->lang('MODRECENT_CONFIRM_DELETE_POST', $title), array(
                'action' => 'delete_post',
                'p' => $post_id,
            ));
        }

        if (!function_exists('delete_posts'))
        {
            include($this->root_path . 'includes/functions_admin.' . $this->php_ext);
        }

        // Also removes the topic when this was its last post.
        delete_posts('post_id', array((int) $post['post_id']));
        $this->log_mod('LOG_MODRECENT_POST_DELETED', $post['forum_id'], 0, array($title));

        $this->done('MODRECENT_POST_DELETED');
    }

    protected function delete_user_posts()
    {
        $user = $this->get_target_user($this->request->variable('u', 0));

        if (!$user)
        {
            trigger_error('NO_AUTH_OPERATION', E_USER_WARNING);
        }

        $count = $this->count_user_posts($user['user_id']);

        if (!confirm_box(true))
        {
            $this->confirm($this->user->lang('MODRECENT_CONFIRM_DELETE_USER_POSTS', $user['username'], $count), array(
                'action' => 'delete_user_posts',
                'u' => $user['user_id'],
            ));
        }

        $deleted = $this->delete_all_posts_of($user['user_id']);
        $this->log_mod('LOG_MODRECENT_USER_POSTS_DELETED', 0, 0, array($user['username'], $deleted));

        $this->done('MODRECENT_USER_POSTS_DELETED', array($deleted, $user['username']));
    }

    protected function delete_user()
    {
        $user = $this->can_delete_users() ? $this->get_target_user($this->request->variable('u', 0)) : false;

        if (!$user)
        {
            trigger_error('NO_AUTH_OPERATION', E_USER_WARNING);
        }

        if (!confirm_box(true))
        {
            $this->confirm($this->user->lang('MODRECENT_CONFIRM_DELETE_USER', $user['username'], $this->count_user_posts($user['user_id'])), array(
                'action' => 'delete_user',
                'u' => $user['user_id'],
            ));
        }

        // Remove the posts first (restricted to forums we may moderate), then the account.
        $this->delete_all_posts_of($user['user_id']);

        if (!function_exists('user_delete'))
        {
            include($this->root_path . 'includes/functions_user.' . $this->php_ext);
        }

        user_delete('remove', array((int) $user['user_id']), true);
        $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MODRECENT_USER_DELETED', time(), array($user['username']));

        $this->done('MODRECENT_USER_DELETED', array($user['username']));
    }

    /**
     * Delete every post by a user within the forums we may moderate.
     */
    protected function delete_all_posts_of($user_id)
    {
        if (!function_exists('delete_posts'))
        {
            include($this->root_path . 'includes/functions_admin.' . $this->php_ext);
        }

        $sql = 'SELECT post_id
            FROM ' . POSTS_TABLE . '
            WHERE poster_id = ' . (int) $user_id . '
                AND ' . $this->db->sql_in_set('forum_id', $this->get_forums());
        $result = $this->db->sql_query($sql);
        $post_ids = array();
        while ($row = $this->db->sql_fetchrow($result))
        {
            $post_ids[] = (int) $row['post_id'];
        }
        $this->db->sql_freeresult($result);

        foreach (array_chunk($post_ids, self::DELETE_CHUNK) as $chunk)
        {
            delete_posts('post_id', $chunk);
        }

        return count($post_ids);
    }

    /**
     * Bulk actions on the checked rows of the topics or posts table.
     */
    protected function bulk()
    {
        $type = $this->request->variable('type', '') === 'posts' ? 'posts' : 'topics';
        $bulk_action = $this->request->variable('bulk_action', '');
        $ids = array_values(array_unique(array_filter($this->request->variable('ids', array(0)), function ($id) {
            return (int) $id > 0;
        })));

        if (!in_array($bulk_action, array('delete_selected', 'delete_users_posts', 'delete_users'), true))
        {
            trigger_error($this->user->lang('MODRECENT_NO_ACTION') . adm_back_link($this->u_action), E_USER_WARNING);
        }

        if ($bulk_action === 'delete_users' && !$this->can_delete_users())
        {
            trigger_error('NO_AUTH_OPERATION', E_USER_WARNING);
        }

        if (!$ids)
        {
            trigger_error($this->user->lang('MODRECENT_NOTHING_SELECTED') . adm_back_link($this->u_action), E_USER_WARNING);
        }

        // Resolve the selected rows, limited to forums we may act on.
        $items = array();
        if ($type === 'topics')
        {
            $sql = 'SELECT topic_id AS item_id, topic_title AS title, topic_poster AS user_id, forum_id
                FROM ' . TOPICS_TABLE . '
                WHERE ' . $this->db->sql_in_set('topic_id', $ids) . '
                    AND ' . $this->db->sql_in_set('forum_id', $this->get_forums());
        }
        else
        {
            $sql = 'SELECT p.post_id AS item_id, p.post_subject AS title, p.poster_id AS user_id, p.forum_id, t.topic_title
                FROM ' . POSTS_TABLE . ' p
                LEFT JOIN ' . TOPICS_TABLE . ' t ON (t.topic_id = p.topic_id)
                WHERE ' . $this->db->sql_in_set('p.post_id', $ids) . '
                    AND ' . $this->db->sql_in_set('p.forum_id', $this->get_forums());
        }
        $result = $this->db->sql_query($sql);
        while ($row = $this->db->sql_fetchrow($result))
        {
            if ($type === 'posts' && $row['title'] === '')
            {
                $row['title'] = (string) $row['topic_title'];
            }
            $items[(int) $row['item_id']] = $row;
        }
        $this->db->sql_freeresult($result);

        if (!$items)
        {
            trigger_error($this->user->lang('MODRECENT_NOTHING_SELECTED') . adm_back_link($this->u_action), E_USER_WARNING);
        }

        // Authors of the selected rows that may be targeted.
        $users = array();
        $skipped = 0;
        if ($bulk_action !== 'delete_selected')
        {
            foreach ($items as $item)
            {
                $uid = (int) $item['user_id'];
                if (isset($users[$uid]))
                {
                    continue;
                }
                $target = $this->get_target_user($uid);
                if ($target)
                {
                    $users[$uid] = $target;
                }
            }

            if (!$users)
            {
                trigger_error($this->user->lang('MODRECENT_NO_VALID_USERS') . adm_back_link($this->u_action), E_USER_WARNING);
            }
        }

        if (!confirm_box(true))
        {
            if ($bulk_action === 'delete_selected')
            {
                $message = $this->user->lang($type === 'topics' ? 'MODRECENT_CONFIRM_BULK_TOPICS' : 'MODRECENT_CONFIRM_BULK_POSTS', count($items));
            }
            else
            {
                $names = array_map(function ($u) {
                    return $u['username'];
                }, array_values($users));
                $shown = implode(', ', array_slice($names, 0, 10)) . (count($names) > 10 ? '…' : '');
                $message = $this->user->lang($bulk_action === 'delete_users' ? 'MODRECENT_CONFIRM_BULK_USERS' : 'MODRECENT_CONFIRM_BULK_USER_POSTS', count($users), $shown);
            }

            $this->confirm($message, array(
                'action' => 'bulk',
                'type' => $type,
                'bulk_action' => $bulk_action,
                'ids' => array_keys($items),
            ));
        }

        if (!function_exists('delete_posts'))
        {
            include($this->root_path . 'includes/functions_admin.' . $this->php_ext);
        }

        if ($bulk_action === 'delete_selected')
        {
            if ($type === 'topics')
            {
                foreach (array_chunk(array_keys($items), self::DELETE_CHUNK) as $chunk)
                {
                    delete_topics('topic_id', $chunk);
                }
                foreach ($items as $item)
                {
                    $this->log_mod('LOG_MODRECENT_TOPIC_DELETED', $item['forum_id'], 0, array($item['title']));
                }
            }
            else
            {
                foreach (array_chunk(array_keys($items), self::DELETE_CHUNK) as $chunk)
                {
                    delete_posts('post_id', $chunk);
                }
                foreach ($items as $item)
                {
                    $this->log_mod('LOG_MODRECENT_POST_DELETED', $item['forum_id'], 0, array($item['title']));
                }
            }

            $this->done('MODRECENT_BULK_DONE', array(count($items)));
        }

        $post_total = 0;
        foreach ($users as $uid => $target)
        {
            $deleted = $this->delete_all_posts_of($uid);
            $post_total += $deleted;

            if ($bulk_action === 'delete_users')
            {
                if (!function_exists('user_delete'))
                {
                    include($this->root_path . 'includes/functions_user.' . $this->php_ext);
                }
                user_delete('remove', array((int) $uid), true);
                $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MODRECENT_USER_DELETED', time(), array($target['username']));
            }
            else
            {
                $this->log_mod('LOG_MODRECENT_USER_POSTS_DELETED', 0, 0, array($target['username'], $deleted));
            }
        }

        $this->done($bulk_action === 'delete_users' ? 'MODRECENT_BULK_USERS_DONE' : 'MODRECENT_BULK_USER_POSTS_DONE', array(count($users), $post_total));
    }

    protected function display_lists()
    {
        $days = max(1, min(365, $this->request->variable('days', self::DEFAULT_DAYS)));
        $limit = $this->request->variable('limit', self::DEFAULT_LIMIT);
        $limit = in_array($limit, array(25, 50, 100, 200), true) ? $limit : self::DEFAULT_LIMIT;
        $since = time() - ($days * 86400);
        $forum_in = $this->db->sql_in_set('forum_id', $this->get_forums());
        $can_delete_users = $this->can_delete_users();
        $self_id = (int) $this->user->data['user_id'];
        $target_cache = array();

        $sql = 'SELECT t.topic_id, t.forum_id, t.topic_title, t.topic_time, t.topic_poster,
                t.topic_first_poster_name, t.topic_first_poster_colour, t.topic_posts_approved,
                f.forum_name, p.poster_ip
            FROM ' . TOPICS_TABLE . ' t
            LEFT JOIN ' . FORUMS_TABLE . ' f ON (f.forum_id = t.forum_id)
            LEFT JOIN ' . POSTS_TABLE . ' p ON (p.post_id = t.topic_first_post_id)
            WHERE ' . str_replace('forum_id', 't.forum_id', $forum_in) . "
                AND t.topic_time >= $since
            ORDER BY t.topic_time DESC";
        $result = $this->db->sql_query_limit($sql, $limit);
        while ($row = $this->db->sql_fetchrow($result))
        {
            $uid = (int) $row['topic_poster'];
            $this->template->assign_block_vars('topics', array(
                'ID' => (int) $row['topic_id'],
                'TITLE' => $row['topic_title'],
                'FORUM' => $row['forum_name'],
                'USER' => get_username_string('full', $uid, $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
                'IP' => $row['poster_ip'],
                'TIME' => $this->user->format_date($row['topic_time']),
                'REPLIES' => max(0, (int) $row['topic_posts_approved'] - 1),
                'U_TOPIC' => append_sid(generate_board_url() . '/viewtopic.' . $this->php_ext, 't=' . (int) $row['topic_id']),
                'U_DELETE_TOPIC' => $this->action_url('delete_topic', array('t' => (int) $row['topic_id']), $days, $limit),
            ) + $this->user_actions($uid, $days, $limit, $can_delete_users, $self_id, $target_cache));
        }
        $this->db->sql_freeresult($result);

        $sql = 'SELECT p.post_id, p.topic_id, p.forum_id, p.poster_id, p.post_time, p.post_subject,
                p.post_text, p.poster_ip, t.topic_title,
                f.forum_name, u.username, u.user_colour
            FROM ' . POSTS_TABLE . ' p
            LEFT JOIN ' . TOPICS_TABLE . ' t ON (t.topic_id = p.topic_id)
            LEFT JOIN ' . FORUMS_TABLE . ' f ON (f.forum_id = p.forum_id)
            LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = p.poster_id)
            WHERE ' . str_replace('forum_id', 'p.forum_id', $forum_in) . "
                AND p.post_time >= $since
            ORDER BY p.post_time DESC";
        $result = $this->db->sql_query_limit($sql, $limit);
        while ($row = $this->db->sql_fetchrow($result))
        {
            $uid = (int) $row['poster_id'];
            $title = $row['post_subject'] !== '' ? $row['post_subject'] : $row['topic_title'];
            $this->template->assign_block_vars('posts', array(
                'ID' => (int) $row['post_id'],
                'TITLE' => $title,
                'EXCERPT' => $this->excerpt($row['post_text']),
                'FORUM' => $row['forum_name'],
                'USER' => get_username_string('full', $uid, $row['username'], $row['user_colour']),
                'IP' => $row['poster_ip'],
                'TIME' => $this->user->format_date($row['post_time']),
                'U_POST' => append_sid(generate_board_url() . '/viewtopic.' . $this->php_ext, 'p=' . (int) $row['post_id']) . '#p' . (int) $row['post_id'],
                'U_DELETE_POST' => $this->action_url('delete_post', array('p' => (int) $row['post_id']), $days, $limit),
            ) + $this->user_actions($uid, $days, $limit, $can_delete_users, $self_id, $target_cache));
        }
        $this->db->sql_freeresult($result);

        $day_options = array(1, 3, 7, 14, 30, 90, 365);
        foreach ($day_options as $option)
        {
            $this->template->assign_block_vars('days_options', array(
                'VALUE' => $option,
                'SELECTED' => $option === $days,
            ));
        }
        foreach (array(25, 50, 100, 200) as $option)
        {
            $this->template->assign_block_vars('limit_options', array(
                'VALUE' => $option,
                'SELECTED' => $option === $limit,
            ));
        }

        $this->template->assign_vars(array(
            'U_ACTION' => $this->u_action,
            'S_CAN_DELETE_USERS' => $can_delete_users,
            'MODRECENT_DAYS' => $days,
            'MODRECENT_LIMIT' => $limit,
        ));
    }

    protected function user_actions($uid, $days, $limit, $can_delete_users, $self_id, array &$cache)
    {
        if (!isset($cache[$uid]))
        {
            $cache[$uid] = ($uid !== $self_id && $this->get_target_user($uid)) ? true : false;
        }

        if (!$cache[$uid])
        {
            return array('S_CAN_USER_ACTIONS' => false, 'S_CAN_DELETE_USER' => false);
        }

        return array(
            'S_CAN_USER_ACTIONS' => true,
            'S_CAN_DELETE_USER' => $can_delete_users,
            'U_DELETE_USER_POSTS' => $this->action_url('delete_user_posts', array('u' => $uid), $days, $limit),
            'U_DELETE_USER' => $this->action_url('delete_user', array('u' => $uid), $days, $limit),
        );
    }

    protected function action_url($action, $params, $days, $limit)
    {
        return $this->list_url(array_merge(array('action' => $action), $params, array('days' => $days, 'limit' => $limit)));
    }

    protected function excerpt($post_text)
    {
        // Stored text is s9e XML: text nodes are already entity-encoded, so stripping tags is safe.
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($post_text)));

        return utf8_strlen($text) > 160 ? utf8_substr($text, 0, 160) . '…' : $text;
    }
}
