<?php
/**
 * Markdown Controller for converting pages to markdown
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\controller;

use Symfony\Component\HttpFoundation\Response;

class markdown_controller
{
    protected $config;
    protected $db;
    protected $auth;
    protected $user;
    protected $language;
    protected $request;
    protected $markdown_converter;
    protected $cache;
    protected $root_path;
    protected $php_ext;

    /**
     * Constructor
     */
    public function __construct(
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\auth\auth $auth,
        \phpbb\user $user,
        \phpbb\language\language $language,
        \phpbb\request\request $request,
        \topofgames\phpbb_llms\service\markdown_converter $markdown_converter,
        \phpbb\cache\driver\driver_interface $cache,
        $root_path,
        $php_ext
    )
    {
        $this->config = $config;
        $this->db = $db;
        $this->auth = $auth;
        $this->user = $user;
        $this->language = $language;
        $this->request = $request;
        $this->markdown_converter = $markdown_converter;
        $this->cache = $cache;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
    }

    /**
     * Handle forum markdown request
     *
     * @return Response
     */
    public function handle_forum_markdown()
    {
        // Check if feature is enabled
        if (!$this->config['llmstxt_enabled'])
        {
            return new Response('Not Found', 404);
        }

        $forum_id = $this->request->variable('f', 0);
        
        if (!$forum_id)
        {
            return new Response('Bad Request', 400);
        }

        // Check permissions
        if (!$this->auth->acl_get('f_list', $forum_id) || !$this->auth->acl_get('f_read', $forum_id))
        {
            return new Response('Forbidden', 403);
        }

        // Try cache
        $cache_key = '_llmstxt_forum_md_' . $forum_id;
        $cache_time = (int) $this->config['llmstxt_cache_time'];
        
        if ($cache_time > 0)
        {
            $content = $this->cache->get($cache_key);
            if ($content !== false)
            {
                return $this->markdown_response($content);
            }
        }

        // Generate markdown
        $content = $this->markdown_converter->convert_forum_to_markdown($forum_id);
        
        if ($content === false)
        {
            return new Response('Not Found', 404);
        }

        // Store in cache
        if ($cache_time > 0)
        {
            $this->cache->put($cache_key, $content, $cache_time);
        }

        return $this->markdown_response($content);
    }

    /**
     * Handle topic markdown request
     *
     * @return Response
     */
    public function handle_topic_markdown()
    {
        // Check if feature is enabled
        if (!$this->config['llmstxt_enabled'])
        {
            return new Response('Not Found', 404);
        }

        $topic_id = $this->request->variable('t', 0);
        
        if (!$topic_id)
        {
            return new Response('Bad Request', 400);
        }

        // Get topic info
        $sql = 'SELECT forum_id, topic_title
                FROM ' . TOPICS_TABLE . '
                WHERE topic_id = ' . (int) $topic_id;
        $result = $this->db->sql_query($sql);
        $topic_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$topic_data)
        {
            return new Response('Not Found', 404);
        }

        // Check permissions
        if (!$this->auth->acl_get('f_read', $topic_data['forum_id']))
        {
            return new Response('Forbidden', 403);
        }

        // Try cache
        $cache_key = '_llmstxt_topic_md_' . $topic_id;
        $cache_time = (int) $this->config['llmstxt_cache_time'];
        
        if ($cache_time > 0)
        {
            $content = $this->cache->get($cache_key);
            if ($content !== false)
            {
                return $this->markdown_response($content);
            }
        }

        // Generate markdown
        $content = $this->markdown_converter->convert_topic_to_markdown($topic_id);
        
        if ($content === false)
        {
            return new Response('Not Found', 404);
        }

        // Store in cache
        if ($cache_time > 0)
        {
            $this->cache->put($cache_key, $content, $cache_time);
        }

        return $this->markdown_response($content);
    }

    /**
     * Create a markdown response
     *
     * @param string $content
     * @return Response
     */
    protected function markdown_response($content)
    {
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/markdown; charset=UTF-8');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        
        return $response;
    }
}