<?php
/**
 *
 * Moderate Recent. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 Ashish Patel <https://ashishpatel.dev>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ashishpatel\modrecent;

class ext extends \phpbb\extension\base
{
    /**
     * Requires phpBB 3.3.0 or newer (but not 4.x) and PHP 7.4 or newer.
     *
     * @return bool
     */
    public function is_enableable()
    {
        $config = $this->container->get('config');

        return phpbb_version_compare($config['version'], '3.3.0', '>=')
            && phpbb_version_compare($config['version'], '4.0.0@dev', '<')
            && version_compare(PHP_VERSION, '7.4.0', '>=');
    }
}
