<?php
/**
 * LLMs.txt Generator Extension
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms;

class ext extends \phpbb\extension\base
{
    /**
     * Check whether the extension can be enabled.
     * Requires phpBB 3.3.0 or higher and PHP 7.2.0 or higher.
     *
     * @return bool|array True if can be enabled, array of error messages otherwise
     */
    public function is_enableable()
    {
        $config = $this->container->get('config');
        
        // Check phpBB version
        if (phpbb_version_compare($config['version'], '3.3.0', '<'))
        {
            return false;
        }
        
        // Check PHP version
        if (phpbb_version_compare(PHP_VERSION, '7.2.0', '<'))
        {
            return false;
        }
        
        return true;
    }
}