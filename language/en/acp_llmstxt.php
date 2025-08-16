<?php
/**
 * ACP Language File
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = [];
}

$lang = array_merge($lang, [
    // Module
    'ACP_LLMSTXT_TITLE'                     => 'LLMs.txt Generator',
    'ACP_LLMSTXT_SETTINGS'                  => 'Settings',
    'ACP_LLMSTXT_SETTINGS_EXPLAIN'          => 'Configure the LLMs.txt generator extension which provides machine-readable documentation for Large Language Models.',
    'ACP_LLMSTXT_SETTINGS_SAVED'            => 'LLMs.txt settings have been successfully saved.',
    
    // General Settings
    'ACP_LLMSTXT_GENERAL_SETTINGS'          => 'General Settings',
    'ACP_LLMSTXT_ENABLED'                   => 'Enable LLMs.txt',
    'ACP_LLMSTXT_ENABLED_EXPLAIN'           => 'Enable or disable the LLMs.txt generator. When enabled, your forum will provide llms.txt and llms-full.txt endpoints.',
    'ACP_LLMSTXT_HEADER'                    => 'Custom header text',
    'ACP_LLMSTXT_HEADER_EXPLAIN'            => 'Optional custom text to include at the top of the llms.txt file. Use this to provide additional context about your forum.',
    
    // Content Settings
    'ACP_LLMSTXT_CONTENT_SETTINGS'          => 'Content Settings',
    'ACP_LLMSTXT_INCLUDE_STATS'             => 'Include forum statistics',
    'ACP_LLMSTXT_INCLUDE_STATS_EXPLAIN'     => 'Include total users, posts, topics, and files in the output.',
    'ACP_LLMSTXT_INCLUDE_RECENT'            => 'Include recent announcements',
    'ACP_LLMSTXT_INCLUDE_RECENT_EXPLAIN'    => 'Include a list of recent global and forum announcements.',
    'ACP_LLMSTXT_MAX_FORUMS'                => 'Maximum forums',
    'ACP_LLMSTXT_MAX_FORUMS_EXPLAIN'        => 'Maximum number of forums to include in the structure (1-100).',
    'ACP_LLMSTXT_MAX_TOPICS'                => 'Maximum popular topics',
    'ACP_LLMSTXT_MAX_TOPICS_EXPLAIN'        => 'Maximum number of popular topics to include in llms-full.txt (1-50).',
    'ACP_LLMSTXT_MAX_ANNOUNCEMENTS'         => 'Maximum announcements',
    'ACP_LLMSTXT_MAX_ANNOUNCEMENTS_EXPLAIN' => 'Maximum number of recent announcements to include (0-20).',
    
    // Cache Settings
    'ACP_LLMSTXT_CACHE_SETTINGS'            => 'Cache Settings',
    'ACP_LLMSTXT_CACHE_TIME'                => 'Cache time for llms.txt',
    'ACP_LLMSTXT_CACHE_TIME_EXPLAIN'        => 'How long to cache the main llms.txt file in seconds. Set to 0 to disable caching.',
    'ACP_LLMSTXT_CACHE_TIME_FULL'           => 'Cache time for llms-full.txt',
    'ACP_LLMSTXT_CACHE_TIME_FULL_EXPLAIN'   => 'How long to cache the extended llms-full.txt file in seconds. Set to 0 to disable caching.',
    
    // Access URLs
    'ACP_LLMSTXT_ACCESS_URLS'               => 'Access URLs',
    'ACP_LLMSTXT_ACCESS_URLS_EXPLAIN'       => 'These are the URLs where the LLMs.txt files can be accessed.',
    'ACP_LLMSTXT_MAIN_URL'                  => 'Main LLMs.txt URL',
    'ACP_LLMSTXT_FULL_URL'                  => 'Extended LLMs.txt URL',
]);