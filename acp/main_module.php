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

class main_module
{
    public $page_title;
    public $tpl_name;
    public $u_action;

    public function main($id, $mode)
    {
        global $phpbb_container, $user;

        $this->tpl_name = 'acp_modrecent';
        $this->page_title = $user->lang('ACP_MODRECENT_TITLE');

        /** @var \ashishpatel\modrecent\acp\controller $controller */
        $controller = $phpbb_container->get('ashishpatel.modrecent.admin_controller');
        $controller->set_page_url($this->u_action);
        $controller->handle();
    }
}
