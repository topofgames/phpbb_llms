<?php
/**
 * ACP Module Info
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\acp;

class main_info
{
    public function module()
    {
        return [
            'filename'  => '\topofgames\phpbb_llms\acp\main_module',
            'title'     => 'ACP_LLMSTXT_TITLE',
            'modes'     => [
                'settings'  => [
                    'title' => 'ACP_LLMSTXT_SETTINGS',
                    'auth'  => 'acl_a_board',
                    'cat'   => ['ACP_LLMSTXT_TITLE']
                ],
            ],
        ];
    }
}