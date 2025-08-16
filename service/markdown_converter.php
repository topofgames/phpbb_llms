<?php
/**
 * Markdown Converter Service
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\service;

class markdown_converter
{
    protected $config;
    protected $db;
    protected $user;
    protected $language;
    protected $root_path;
    protected $php_ext;

    /**
     * Constructor
     */
    public function __construct(
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\user $user,
        \phpbb\language\language $language,
        $root_path,
        $php_ext
    )
    {
        $this->config = $config;
        $this->db = $db;
        $this->user = $user;
        $this->language = $language;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
    }

    /**
     * Convert forum page to markdown
     *
     * @param int $forum_id
     * @return string|false
     */
    public function convert_forum_to_markdown($forum_id)
    {
        // Get forum data
        $sql = 'SELECT *
                FROM ' . FORUMS_TABLE . '
                WHERE forum_id = ' . (int) $forum_id;
        $result = $this->db->sql_query($sql);
        $forum_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$forum_data)
        {
            return false;
        }

        $output = [];
        
        // Forum header
        $output[] = '# Forum: ' . $this->clean_text($forum_data['forum_name']);
        $output[] = '';
        
        if (!empty($forum_data['forum_desc']))
        {
            $output[] = '> ' . $this->clean_text($forum_data['forum_desc']);
            $output[] = '';
        }
        
        // Forum statistics
        $output[] = '## Statistics';
        $output[] = '- Topics: ' . number_format($forum_data['forum_topics_approved']);
        $output[] = '- Posts: ' . number_format($forum_data['forum_posts_approved']);
        $output[] = '';
        
        // Forum rules
        if (!empty($forum_data['forum_rules']))
        {
            $output[] = '## Forum Rules';
            $output[] = $this->bbcode_to_markdown($forum_data['forum_rules']);
            $output[] = '';
        }
        
        // Get subforums
        $sql = 'SELECT forum_id, forum_name, forum_desc, forum_topics_approved, forum_posts_approved
                FROM ' . FORUMS_TABLE . '
                WHERE parent_id = ' . (int) $forum_id . '
                ORDER BY left_id ASC';
        $result = $this->db->sql_query($sql);
        
        $subforums = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            $subforums[] = $row;
        }
        $this->db->sql_freeresult($result);
        
        if (!empty($subforums))
        {
            $output[] = '## Subforums';
            foreach ($subforums as $subforum)
            {
                $output[] = '### ' . $this->clean_text($subforum['forum_name']);
                if (!empty($subforum['forum_desc']))
                {
                    $output[] = $this->clean_text($subforum['forum_desc']);
                }
                $output[] = '- Topics: ' . number_format($subforum['forum_topics_approved']);
                $output[] = '- Posts: ' . number_format($subforum['forum_posts_approved']);
                $output[] = '';
            }
        }
        
        // Get topics
        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_posts_approved, t.topic_views, 
                       t.topic_last_post_time, u.username
                FROM ' . TOPICS_TABLE . ' t
                LEFT JOIN ' . USERS_TABLE . ' u ON u.user_id = t.topic_poster
                WHERE t.forum_id = ' . (int) $forum_id . '
                    AND t.topic_visibility = ' . ITEM_APPROVED . '
                ORDER BY t.topic_type DESC, t.topic_last_post_time DESC
                LIMIT 25';
        $result = $this->db->sql_query($sql);
        
        $topics = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            $topics[] = $row;
        }
        $this->db->sql_freeresult($result);
        
        if (!empty($topics))
        {
            $output[] = '## Recent Topics';
            $output[] = '';
            $output[] = '| Title | Author | Replies | Views | Last Post |';
            $output[] = '|-------|--------|---------|-------|-----------|';
            
            foreach ($topics as $topic)
            {
                $output[] = sprintf(
                    '| %s | %s | %d | %d | %s |',
                    $this->clean_text($topic['topic_title']),
                    $topic['username'],
                    $topic['topic_posts_approved'] - 1,
                    $topic['topic_views'],
                    date('Y-m-d', $topic['topic_last_post_time'])
                );
            }
            $output[] = '';
        }
        
        // Footer
        $output[] = '---';
        $output[] = 'Generated: ' . date('Y-m-d H:i:s');
        
        return implode("\n", $output);
    }

    /**
     * Convert topic page to markdown
     *
     * @param int $topic_id
     * @return string|false
     */
    public function convert_topic_to_markdown($topic_id)
    {
        // Get topic data
        $sql = 'SELECT t.*, f.forum_name, u.username
                FROM ' . TOPICS_TABLE . ' t
                LEFT JOIN ' . FORUMS_TABLE . ' f ON f.forum_id = t.forum_id
                LEFT JOIN ' . USERS_TABLE . ' u ON u.user_id = t.topic_poster
                WHERE t.topic_id = ' . (int) $topic_id;
        $result = $this->db->sql_query($sql);
        $topic_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$topic_data)
        {
            return false;
        }

        $output = [];
        
        // Topic header
        $output[] = '# ' . $this->clean_text($topic_data['topic_title']);
        $output[] = '';
        $output[] = '**Forum:** ' . $this->clean_text($topic_data['forum_name']);
        $output[] = '**Started by:** ' . $topic_data['username'];
        $output[] = '**Date:** ' . date('Y-m-d H:i', $topic_data['topic_time']);
        $output[] = '**Replies:** ' . ($topic_data['topic_posts_approved'] - 1);
        $output[] = '**Views:** ' . number_format($topic_data['topic_views']);
        $output[] = '';
        $output[] = '---';
        $output[] = '';
        
        // Get posts
        $sql = 'SELECT p.*, u.username, u.user_posts, u.user_regdate
                FROM ' . POSTS_TABLE . ' p
                LEFT JOIN ' . USERS_TABLE . ' u ON u.user_id = p.poster_id
                WHERE p.topic_id = ' . (int) $topic_id . '
                    AND p.post_visibility = ' . ITEM_APPROVED . '
                ORDER BY p.post_time ASC
                LIMIT 50';
        $result = $this->db->sql_query($sql);
        
        $post_num = 0;
        while ($row = $this->db->sql_fetchrow($result))
        {
            $post_num++;
            
            $output[] = '## Post #' . $post_num;
            $output[] = '';
            $output[] = '**Author:** ' . $row['username'];
            $output[] = '**Posted:** ' . date('Y-m-d H:i', $row['post_time']);
            
            if ($row['post_edit_time'])
            {
                $output[] = '**Last edited:** ' . date('Y-m-d H:i', $row['post_edit_time']);
            }
            
            $output[] = '';
            
            // Convert post text
            $post_text = $this->bbcode_to_markdown($row['post_text']);
            $output[] = $post_text;
            $output[] = '';
            $output[] = '---';
            $output[] = '';
        }
        $this->db->sql_freeresult($result);
        
        // Footer
        $output[] = 'Generated: ' . date('Y-m-d H:i:s');
        
        return implode("\n", $output);
    }

    /**
     * Convert BBCode to Markdown
     *
     * @param string $text
     * @return string
     */
    protected function bbcode_to_markdown($text)
    {
        // Decode HTML entities first
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Convert BBCode to Markdown
        $bbcode_patterns = [
            '/\[b\](.*?)\[\/b\]/is' => '**$1**',
            '/\[i\](.*?)\[\/i\]/is' => '*$1*',
            '/\[u\](.*?)\[\/u\]/is' => '__$1__',
            '/\[code\](.*?)\[\/code\]/is' => "\n```\n$1\n```\n",
            '/\[code=([a-zA-Z0-9]+)\](.*?)\[\/code\]/is' => "\n```$1\n$2\n```\n",
            '/\[quote\](.*?)\[\/quote\]/is' => "\n> $1\n",
            '/\[quote="([^"]+)"\](.*?)\[\/quote\]/is' => "\n> **$1 wrote:**\n> $2\n",
            '/\[url\](.*?)\[\/url\]/is' => '[$1]($1)',
            '/\[url=(.*?)\](.*?)\[\/url\]/is' => '[$2]($1)',
            '/\[img\](.*?)\[\/img\]/is' => '![]($1)',
            '/\[list\](.*?)\[\/list\]/is' => "$1",
            '/\[list=1\](.*?)\[\/list\]/is' => "$1",
            '/\[\*\](.*)(\n|$)/is' => "- $1\n",
            '/\[color=[^\]]+\](.*?)\[\/color\]/is' => '$1',
            '/\[size=[^\]]+\](.*?)\[\/size\]/is' => '$1',
            '/\[email\](.*?)\[\/email\]/is' => '[$1](mailto:$1)',
            '/\[email=(.*?)\](.*?)\[\/email\]/is' => '[$2](mailto:$1)',
        ];
        
        foreach ($bbcode_patterns as $pattern => $replacement)
        {
            $text = preg_replace($pattern, $replacement, $text);
        }
        
        // Clean up smilies
        $text = preg_replace('/<img[^>]+alt="([^"]+)"[^>]*>/i', '$1', $text);
        
        // Remove any remaining BBCode tags
        $text = preg_replace('/\[[^\]]*\]/', '', $text);
        
        // Convert line breaks
        $text = str_replace(['<br />', '<br>', '<br/>'], "\n", $text);
        
        // Clean up multiple newlines
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        
        return trim($text);
    }

    /**
     * Clean text for output
     *
     * @param string $text
     * @return string
     */
    protected function clean_text($text)
    {
        // Remove BBCode
        $text = preg_replace('/\[.*?\]/', '', $text);
        
        // Convert HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // Remove HTML tags
        $text = strip_tags($text);
        
        // Clean whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
}