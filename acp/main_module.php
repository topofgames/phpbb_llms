<?php
/**
 * ACP Module
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\acp;

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    public function main($id, $mode)
    {
        global $config, $request, $template, $user, $cache, $phpbb_container;

        $user->add_lang_ext('topofgames/phpbb_llms', 'acp_llmstxt');
        
        $this->tpl_name = 'acp_llmstxt';
        $this->page_title = $user->lang('ACP_LLMSTXT_SETTINGS');

        add_form_key('topofgames_llms_settings');

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('topofgames_llms_settings'))
            {
                trigger_error('FORM_INVALID', E_USER_WARNING);
            }

            // Update configuration
            $config->set('llmstxt_enabled', $request->variable('llmstxt_enabled', 0));
            $config->set('llmstxt_cache_time', $request->variable('llmstxt_cache_time', 3600));
            $config->set('llmstxt_cache_time_full', $request->variable('llmstxt_cache_time_full', 21600));
            $config->set('llmstxt_include_stats', $request->variable('llmstxt_include_stats', 1));
            $config->set('llmstxt_include_recent', $request->variable('llmstxt_include_recent', 1));
            $config->set('llmstxt_max_forums', $request->variable('llmstxt_max_forums', 50));
            $config->set('llmstxt_max_topics', $request->variable('llmstxt_max_topics', 10));
            $config->set('llmstxt_max_announcements', $request->variable('llmstxt_max_announcements', 5));
            $config->set('llmstxt_header', $request->variable('llmstxt_header', '', true));

            // Clear cache
            $cache->purge();

            trigger_error($user->lang('ACP_LLMSTXT_SETTINGS_SAVED') . adm_back_link($this->u_action));
        }

        // Display current settings
        $template->assign_vars([
            'U_ACTION'                      => $this->u_action,
            'LLMSTXT_ENABLED'              => $config['llmstxt_enabled'],
            'LLMSTXT_CACHE_TIME'           => $config['llmstxt_cache_time'],
            'LLMSTXT_CACHE_TIME_FULL'      => $config['llmstxt_cache_time_full'],
            'LLMSTXT_INCLUDE_STATS'        => $config['llmstxt_include_stats'],
            'LLMSTXT_INCLUDE_RECENT'       => $config['llmstxt_include_recent'],
            'LLMSTXT_MAX_FORUMS'           => $config['llmstxt_max_forums'],
            'LLMSTXT_MAX_TOPICS'           => $config['llmstxt_max_topics'],
            'LLMSTXT_MAX_ANNOUNCEMENTS'    => $config['llmstxt_max_announcements'],
            'LLMSTXT_HEADER'               => $config['llmstxt_header'],
            'LLMSTXT_URL'                  => generate_board_url() . '/llms.txt',
            'LLMSTXT_FULL_URL'             => generate_board_url() . '/llms-full.txt',
        ]);
    }
}