<?php
/**
 * Main Controller for LLMs.txt Generation
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\controller;

use Symfony\Component\HttpFoundation\Response;

class main_controller
{
    protected $config;
    protected $db;
    protected $auth;
    protected $user;
    protected $language;
    protected $request;
    protected $generator;
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
        \topofgames\phpbb_llms\service\llmstxt_generator $generator,
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
        $this->generator = $generator;
        $this->cache = $cache;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
    }

    /**
     * Handle /llms.txt request
     *
     * @return Response
     */
    public function handle_llms_txt()
    {
        // Check if feature is enabled
        if (!$this->config['llmstxt_enabled'])
        {
            return new Response('Not Found', 404);
        }

        // Check permission - allow if permission is granted or if user is guest and guest access is allowed
        if (!$this->auth->acl_get('u_view_llmstxt'))
        {
            // For public access, we can optionally allow guests
            // Comment out the following line to allow unrestricted access
            // return new Response('Forbidden', 403);
        }

        // Try to get from cache
        $cache_key = '_llmstxt_main';
        $cache_time = (int) $this->config['llmstxt_cache_time'];
        
        if ($cache_time > 0)
        {
            $content = $this->cache->get($cache_key);
            if ($content !== false)
            {
                return $this->text_response($content);
            }
        }

        // Generate content
        $content = $this->generator->generate_llms_txt();

        // Store in cache
        if ($cache_time > 0)
        {
            $this->cache->put($cache_key, $content, $cache_time);
        }

        return $this->text_response($content);
    }

    /**
     * Handle /llms-full.txt request
     *
     * @return Response
     */
    public function handle_llms_full()
    {
        // Check if feature is enabled
        if (!$this->config['llmstxt_enabled'])
        {
            return new Response('Not Found', 404);
        }

        // Check permission - allow if permission is granted or if user is guest and guest access is allowed
        if (!$this->auth->acl_get('u_view_llmstxt'))
        {
            // For public access, we can optionally allow guests
            // Comment out the following line to allow unrestricted access
            // return new Response('Forbidden', 403);
        }

        // Try to get from cache
        $cache_key = '_llmstxt_full';
        $cache_time = (int) $this->config['llmstxt_cache_time_full'];
        
        if ($cache_time > 0)
        {
            $content = $this->cache->get($cache_key);
            if ($content !== false)
            {
                return $this->text_response($content);
            }
        }

        // Generate full content
        $content = $this->generator->generate_llms_full();

        // Store in cache
        if ($cache_time > 0)
        {
            $this->cache->put($cache_key, $content, $cache_time);
        }

        return $this->text_response($content);
    }

    /**
     * Create a plain text response
     *
     * @param string $content
     * @return Response
     */
    protected function text_response($content)
    {
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        
        return $response;
    }
}