<?php
/**
 * Language file for common terms
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
    'LLMSTXT_TITLE'                 => 'LLMs.txt Generator',
    'LLMSTXT_DESCRIPTION'           => 'Generates machine-readable documentation for Large Language Models',
    
    // ACP Module titles (needed for Extensions Manager)
    'ACP_LLMSTXT_TITLE'             => 'LLMs.txt Generator',
    'ACP_LLMSTXT_SETTINGS'          => 'Settings',
    
    // Permissions
    'ACL_U_VIEW_LLMSTXT'            => 'Can view llms.txt files',
    
    // Errors
    'LLMSTXT_NOT_ENABLED'           => 'The LLMs.txt feature is not enabled.',
    'LLMSTXT_NO_PERMISSION'         => 'You do not have permission to view this content.',
    'LLMSTXT_NOT_FOUND'             => 'The requested content was not found.',
]);