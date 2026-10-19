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

class main_info
{
    public function module()
    {
        return array(
            'filename'  => '\ashishpatel\modrecent\acp\main_module',
            'title'     => 'ACP_MODRECENT_TITLE',
            'modes'     => array(
                'moderate' => array(
                    'title' => 'ACP_MODRECENT',
                    'auth'  => 'ext_ashishpatel/modrecent && acl_a_user',
                    'cat'   => array('ACP_MODRECENT_TITLE'),
                ),
            ),
        );
    }
}
