<?php
/**
 * LLMs.txt Generator Service
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\service;

class llmstxt_generator
{
    protected $config;
    protected $db;
    protected $auth;
    protected $user;
    protected $language;
    protected $cache;
    protected $root_path;
    protected $php_ext;
    protected $table_prefix;

    /**
     * Constructor
     */
    public function __construct(
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\auth\auth $auth,
        \phpbb\user $user,
        \phpbb\language\language $language,
        \phpbb\cache\driver\driver_interface $cache,
        $root_path,
        $php_ext,
        $table_prefix
    )
    {
        $this->config = $config;
        $this->db = $db;
        $this->auth = $auth;
        $this->user = $user;
        $this->language = $language;
        $this->cache = $cache;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
    }

    /**
     * Generate the main llms.txt content
     *
     * @return string
     */
    public function generate_llms_txt()
    {
        $output = [];
        
        // Header with site name
        $output[] = '# ' . $this->config['sitename'];
        $output[] = '';
        
        // Site description
        $site_desc = $this->config['site_desc'];
        if (!empty($site_desc))
        {
            $output[] = '> ' . $this->clean_text($site_desc);
            $output[] = '';
        }
        
        // Add custom header if configured
        if (!empty($this->config['llmstxt_header']))
        {
            $output[] = $this->config['llmstxt_header'];
            $output[] = '';
        }
        
        // Forum Statistics
        if ($this->config['llmstxt_include_stats'])
        {
            $output[] = '## Forum Statistics';
            $output[] = '- Total Users: ' . number_format($this->config['num_users']);
            $output[] = '- Total Posts: ' . number_format($this->config['num_posts']);
            $output[] = '- Total Topics: ' . number_format($this->config['num_topics']);
            $output[] = '- Total Files: ' . number_format($this->config['num_files']);
            $output[] = '';
        }
        
        // Forum Structure
        $output[] = '## Forum Structure';
        $output[] = '';
        
        $forums = $this->get_forum_structure();
        foreach ($forums as $forum)
        {
            $this->add_forum_to_output($forum, $output, 0);
        }
        $output[] = '';
        
        // Important Links
        $board_url = $this->generate_board_url();
        $output[] = '## Important Links';
        $output[] = '- [Forum Rules](' . $board_url . '/rules)';
        $output[] = '- [FAQ](' . $board_url . '/faq.' . $this->php_ext . ')';
        $output[] = '- [Search](' . $board_url . '/search.' . $this->php_ext . ')';
        $output[] = '- [Member List](' . $board_url . '/memberlist.' . $this->php_ext . ')';
        
        // RSS/Atom feeds
        if ($this->config['feed_enable'])
        {
            $output[] = '- [RSS Feed](' . $board_url . '/feed.' . $this->php_ext . ')';
        }
        $output[] = '';
        
        // Recent Announcements
        if ($this->config['llmstxt_include_recent'])
        {
            $announcements = $this->get_recent_announcements();
            if (!empty($announcements))
            {
                $output[] = '## Recent Announcements';
                foreach ($announcements as $announcement)
                {
                    $output[] = '- ' . $this->clean_text($announcement['topic_title']) . 
                               ' (' . date('Y-m-d', $announcement['topic_time']) . ')';
                }
                $output[] = '';
            }
        }
        
        // API Information
        $output[] = '## Available APIs';
        $output[] = '- JSON API: Not available in standard phpBB';
        $output[] = '- RSS/Atom Feeds: ' . ($this->config['feed_enable'] ? 'Available' : 'Disabled');
        $output[] = '';
        
        // Footer
        $output[] = '---';
        $output[] = 'Generated: ' . date('Y-m-d H:i:s T');
        $output[] = 'phpBB Version: ' . $this->config['version'];
        
        return implode("\n", $output);
    }

    /**
     * Generate the full llms-full.txt content
     *
     * @return string
     */
    public function generate_llms_full()
    {
        $output = [];
        
        // Start with the basic content
        $basic = $this->generate_llms_txt();
        $output[] = $basic;
        $output[] = '';
        $output[] = '---';
        $output[] = '';
        $output[] = '# Extended Documentation';
        $output[] = '';
        
        // Detailed Forum Information
        $output[] = '## Detailed Forum Categories';
        $output[] = '';
        
        $forums = $this->get_forum_structure(true);
        foreach ($forums as $forum)
        {
            $this->add_detailed_forum_to_output($forum, $output);
        }
        
        // Popular Topics
        $output[] = '## Popular Topics';
        $output[] = '';
        
        $popular_topics = $this->get_popular_topics();
        foreach ($popular_topics as $topic)
        {
            $output[] = '### ' . $this->clean_text($topic['topic_title']);
            $output[] = '- Views: ' . number_format($topic['topic_views']);
            $output[] = '- Replies: ' . number_format($topic['topic_posts_approved'] - 1);
            $output[] = '- Last Post: ' . date('Y-m-d', $topic['topic_last_post_time']);
            $output[] = '';
        }
        
        // BBCode Reference
        $output[] = '## BBCode Reference';
        $output[] = '';
        $output[] = 'The following BBCode tags are available:';
        $output[] = '- `[b]text[/b]` - Bold text';
        $output[] = '- `[i]text[/i]` - Italic text';
        $output[] = '- `[u]text[/u]` - Underlined text';
        $output[] = '- `[code]code[/code]` - Code block';
        $output[] = '- `[quote]text[/quote]` - Quote block';
        $output[] = '- `[url]link[/url]` - URL link';
        $output[] = '- `[img]image_url[/img]` - Embed image';
        $output[] = '- `[list][*]item[/list]` - Bullet list';
        $output[] = '- `[color=red]text[/color]` - Colored text';
        $output[] = '- `[size=150]text[/size]` - Text size';
        $output[] = '';
        
        // Search Capabilities
        $output[] = '## Search Capabilities';
        $output[] = '';
        $output[] = 'The forum search supports:';
        $output[] = '- Keyword search in posts and topics';
        $output[] = '- Author search';
        $output[] = '- Forum-specific search';
        $output[] = '- Date range filtering';
        $output[] = '- Sort by relevance, date, or post count';
        $output[] = '';
        
        // User Ranks
        if ($this->config['ranks_enable'])
        {
            $output[] = '## User Ranks';
            $output[] = '';
            
            $ranks = $this->get_user_ranks();
            foreach ($ranks as $rank)
            {
                $output[] = '- **' . $rank['rank_title'] . '**' . 
                           ($rank['rank_min'] > 0 ? ' (Minimum ' . $rank['rank_min'] . ' posts)' : ' (Special)');
            }
            $output[] = '';
        }
        
        return implode("\n", $output);
    }

    /**
     * Get forum structure
     *
     * @param bool $detailed Include additional details
     * @return array
     */
    protected function get_forum_structure($detailed = false)
    {
        $sql_array = [
            'SELECT'    => 'f.*, ft.mark_time',
            'FROM'      => [
                $this->table_prefix . 'forums' => 'f'
            ],
            'LEFT_JOIN' => [
                [
                    'FROM'  => [$this->table_prefix . 'forums_track' => 'ft'],
                    'ON'    => 'ft.user_id = ' . (int) $this->user->data['user_id'] . ' AND ft.forum_id = f.forum_id'
                ]
            ],
            'WHERE'     => 'f.forum_type != ' . FORUM_LINK,
            'ORDER_BY'  => 'f.left_id ASC'
        ];

        $sql = $this->db->sql_build_query('SELECT', $sql_array);
        $result = $this->db->sql_query($sql);

        $forums = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            // Check permissions
            if (!$this->auth->acl_get('f_list', $row['forum_id']))
            {
                continue;
            }
            
            // Skip password protected forums
            if (!empty($row['forum_password']))
            {
                continue;
            }
            
            $forums[$row['forum_id']] = [
                'forum_id'      => $row['forum_id'],
                'parent_id'     => $row['parent_id'],
                'forum_name'    => $row['forum_name'],
                'forum_desc'    => $row['forum_desc'],
                'forum_type'    => $row['forum_type'],
                'forum_posts'   => $row['forum_posts_approved'],
                'forum_topics'  => $row['forum_topics_approved'],
                'children'      => []
            ];
        }
        $this->db->sql_freeresult($result);

        // Build tree structure
        return $this->build_forum_tree($forums);
    }

    /**
     * Build forum tree from flat array
     *
     * @param array $forums
     * @return array
     */
    protected function build_forum_tree($forums)
    {
        $tree = [];
        
        foreach ($forums as $id => &$forum)
        {
            if ($forum['parent_id'] == 0)
            {
                $tree[$id] = &$forum;
            }
            else if (isset($forums[$forum['parent_id']]))
            {
                $forums[$forum['parent_id']]['children'][$id] = &$forum;
            }
        }
        
        return $tree;
    }

    /**
     * Add forum to output array
     *
     * @param array $forum
     * @param array &$output
     * @param int $level
     */
    protected function add_forum_to_output($forum, &$output, $level)
    {
        $indent = str_repeat('  ', $level);
        
        if ($forum['forum_type'] == FORUM_CAT)
        {
            $output[] = $indent . '### ' . $this->clean_text($forum['forum_name']);
        }
        else
        {
            $output[] = $indent . '- **' . $this->clean_text($forum['forum_name']) . '**';
        }
        
        if (!empty($forum['forum_desc']))
        {
            $output[] = $indent . '  ' . $this->clean_text($forum['forum_desc']);
        }
        
        if (!empty($forum['children']))
        {
            foreach ($forum['children'] as $child)
            {
                $this->add_forum_to_output($child, $output, $level + 1);
            }
        }
    }

    /**
     * Add detailed forum information to output
     *
     * @param array $forum
     * @param array &$output
     */
    protected function add_detailed_forum_to_output($forum, &$output)
    {
        if ($forum['forum_type'] == FORUM_CAT)
        {
            $output[] = '### Category: ' . $this->clean_text($forum['forum_name']);
        }
        else
        {
            $output[] = '### Forum: ' . $this->clean_text($forum['forum_name']);
            $output[] = '- Topics: ' . number_format($forum['forum_topics']);
            $output[] = '- Posts: ' . number_format($forum['forum_posts']);
        }
        
        if (!empty($forum['forum_desc']))
        {
            $output[] = '- Description: ' . $this->clean_text($forum['forum_desc']);
        }
        
        $output[] = '';
        
        if (!empty($forum['children']))
        {
            foreach ($forum['children'] as $child)
            {
                $this->add_detailed_forum_to_output($child, $output);
            }
        }
    }

    /**
     * Get recent announcements
     *
     * @return array
     */
    protected function get_recent_announcements()
    {
        $limit = (int) $this->config['llmstxt_max_announcements'] ?: 5;
        
        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.forum_id
                FROM ' . $this->table_prefix . 'topics t
                WHERE t.topic_type = ' . POST_ANNOUNCE . '
                    OR t.topic_type = ' . POST_GLOBAL . '
                ORDER BY t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $limit);
        
        $announcements = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            // Check permissions
            if ($this->auth->acl_get('f_read', $row['forum_id']))
            {
                $announcements[] = $row;
            }
        }
        $this->db->sql_freeresult($result);
        
        return $announcements;
    }

    /**
     * Get popular topics
     *
     * @return array
     */
    protected function get_popular_topics()
    {
        $limit = (int) $this->config['llmstxt_max_topics'] ?: 10;
        
        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_views, t.topic_posts_approved, 
                       t.topic_last_post_time, t.forum_id
                FROM ' . $this->table_prefix . 'topics t
                WHERE t.topic_visibility = ' . ITEM_APPROVED . '
                ORDER BY t.topic_views DESC';
        $result = $this->db->sql_query_limit($sql, $limit);
        
        $topics = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            // Check permissions
            if ($this->auth->acl_get('f_read', $row['forum_id']))
            {
                $topics[] = $row;
            }
        }
        $this->db->sql_freeresult($result);
        
        return $topics;
    }

    /**
     * Get user ranks
     *
     * @return array
     */
    protected function get_user_ranks()
    {
        $sql = 'SELECT rank_title, rank_min, rank_special
                FROM ' . $this->table_prefix . 'ranks
                ORDER BY rank_special DESC, rank_min ASC';
        $result = $this->db->sql_query($sql);
        
        $ranks = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            $ranks[] = $row;
        }
        $this->db->sql_freeresult($result);
        
        return $ranks;
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
        
        // Remove multiple spaces and newlines
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Trim
        return trim($text);
    }

    /**
     * Generate board URL
     *
     * @return string
     */
    protected function generate_board_url()
    {
        $server_protocol = ($this->config['server_protocol']) ? $this->config['server_protocol'] : 
                          (($this->config['cookie_secure']) ? 'https://' : 'http://');
        $server_name = $this->config['server_name'];
        $server_port = (int) $this->config['server_port'];
        $script_path = $this->config['script_path'];

        $url = $server_protocol . $server_name;

        if ($server_port && (($server_protocol == 'http://' && $server_port != 80) || 
            ($server_protocol == 'https://' && $server_port != 443)))
        {
            $url .= ':' . $server_port;
        }

        $url .= rtrim($script_path, '/');

        return $url;
    }
}