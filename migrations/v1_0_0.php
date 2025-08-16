<?php
/**
 * Migration for initial installation
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\migrations;

class v1_0_0 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['llmstxt_enabled']);
    }

    public static function depends_on()
    {
        return ['\phpbb\db\migration\data\v33x\v331'];
    }

    public function update_data()
    {
        return [
            // Add configuration values
            ['config.add', ['llmstxt_enabled', 1]],
            ['config.add', ['llmstxt_cache_time', 3600]],
            ['config.add', ['llmstxt_cache_time_full', 21600]],
            ['config.add', ['llmstxt_include_stats', 1]],
            ['config.add', ['llmstxt_include_recent', 1]],
            ['config.add', ['llmstxt_max_forums', 50]],
            ['config.add', ['llmstxt_max_topics', 10]],
            ['config.add', ['llmstxt_max_announcements', 5]],
            ['config.add', ['llmstxt_header', '']],
            
            // Add permissions
            ['permission.add', ['u_view_llmstxt', true]],
            
            // Set default permission for registered users
            ['permission.permission_set', ['REGISTERED', 'u_view_llmstxt', 'group']],
            ['permission.permission_set', ['REGISTERED_COPPA', 'u_view_llmstxt', 'group']],
            
            // Add ACP module
            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_LLMSTXT_TITLE'
            ]],
            ['module.add', [
                'acp',
                'ACP_LLMSTXT_TITLE',
                [
                    'module_basename'   => '\topofgames\phpbb_llms\acp\main_module',
                    'modes'             => ['settings'],
                ],
            ]],
        ];
    }

    public function revert_data()
    {
        return [
            // Remove configuration values
            ['config.remove', ['llmstxt_enabled']],
            ['config.remove', ['llmstxt_cache_time']],
            ['config.remove', ['llmstxt_cache_time_full']],
            ['config.remove', ['llmstxt_include_stats']],
            ['config.remove', ['llmstxt_include_recent']],
            ['config.remove', ['llmstxt_max_forums']],
            ['config.remove', ['llmstxt_max_topics']],
            ['config.remove', ['llmstxt_max_announcements']],
            ['config.remove', ['llmstxt_header']],
            
            // Remove permissions
            ['permission.remove', ['u_view_llmstxt']],
            
            // Remove ACP module
            ['module.remove', [
                'acp',
                'ACP_LLMSTXT_TITLE',
                [
                    'module_basename'   => '\topofgames\phpbb_llms\acp\main_module',
                    'modes'             => ['settings'],
                ],
            ]],
            ['module.remove', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_LLMSTXT_TITLE'
            ]],
        ];
    }
}