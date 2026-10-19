<?php
/**
 *
 * Moderate Recent. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 Ashish Patel <https://ashishpatel.dev>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ashishpatel\modrecent\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        $sql = 'SELECT module_id
            FROM ' . $this->table_prefix . "modules
            WHERE module_class = 'acp'
                AND module_langname = 'ACP_MODRECENT_TITLE'";
        $result = $this->db->sql_query($sql);
        $module_id = $this->db->sql_fetchfield('module_id');
        $this->db->sql_freeresult($result);

        return $module_id !== false;
    }

    public static function depends_on()
    {
        return array('\phpbb\db\migration\data\v330\v330');
    }

    public function update_data()
    {
        return array(
            array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_MODRECENT_TITLE')),
            array('module.add', array('acp', 'ACP_MODRECENT_TITLE', array(
                'module_basename' => '\ashishpatel\modrecent\acp\main_module',
                'modes'           => array('moderate'),
            ))),
        );
    }
}
