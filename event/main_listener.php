<?php
/**
 * Event Listener for cache invalidation
 *
 * @package topofgames/phpbb-llms
 * @copyright (c) 2024 TopOfGames
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace topofgames\phpbb_llms\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
    protected $cache;
    protected $config;

    /**
     * Constructor
     *
     * @param \phpbb\cache\driver\driver_interface $cache
     * @param \phpbb\config\config $config
     */
    public function __construct(\phpbb\cache\driver\driver_interface $cache, \phpbb\config\config $config)
    {
        $this->cache = $cache;
        $this->config = $config;
    }

    /**
     * Assign functions to event listeners
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'core.submit_post_end'              => 'invalidate_cache_on_post',
            'core.delete_posts_after'           => 'invalidate_cache_on_delete',
            'core.move_topics_after'            => 'invalidate_cache_on_move',
            'core.acp_manage_forums_update_data_after' => 'invalidate_cache_on_forum_change',
            'core.acp_board_config_edit_add'    => 'invalidate_cache_on_config_change',
        ];
    }

    /**
     * Invalidate cache when a new post is made
     *
     * @param \phpbb\event\data $event
     */
    public function invalidate_cache_on_post($event)
    {
        if (!$this->config['llmstxt_enabled'])
        {
            return;
        }

        $data = $event['data'];
        
        // Invalidate cache for announcements
        if ($data['topic_type'] == POST_ANNOUNCE || $data['topic_type'] == POST_GLOBAL)
        {
            $this->clear_llmstxt_cache();
        }
        
        // Invalidate topic markdown cache if exists
        if (!empty($data['topic_id']))
        {
            $this->cache->destroy('_llmstxt_topic_md_' . $data['topic_id']);
        }
        
        // Invalidate forum markdown cache if exists
        if (!empty($data['forum_id']))
        {
            $this->cache->destroy('_llmstxt_forum_md_' . $data['forum_id']);
        }
    }

    /**
     * Invalidate cache when posts are deleted
     *
     * @param \phpbb\event\data $event
     */
    public function invalidate_cache_on_delete($event)
    {
        if (!$this->config['llmstxt_enabled'])
        {
            return;
        }

        $this->clear_llmstxt_cache();
    }

    /**
     * Invalidate cache when topics are moved
     *
     * @param \phpbb\event\data $event
     */
    public function invalidate_cache_on_move($event)
    {
        if (!$this->config['llmstxt_enabled'])
        {
            return;
        }

        $this->clear_llmstxt_cache();
    }

    /**
     * Invalidate cache when forum structure changes
     *
     * @param \phpbb\event\data $event
     */
    public function invalidate_cache_on_forum_change($event)
    {
        if (!$this->config['llmstxt_enabled'])
        {
            return;
        }

        $this->clear_llmstxt_cache();
    }

    /**
     * Invalidate cache when board configuration changes
     *
     * @param \phpbb\event\data $event
     */
    public function invalidate_cache_on_config_change($event)
    {
        if (!$this->config['llmstxt_enabled'])
        {
            return;
        }

        $cfg_array = $event['cfg_array'];
        
        // Check if relevant config values changed
        $relevant_configs = [
            'sitename',
            'site_desc',
            'feed_enable',
            'ranks_enable',
        ];
        
        foreach ($relevant_configs as $config_name)
        {
            if (isset($cfg_array[$config_name]))
            {
                $this->clear_llmstxt_cache();
                break;
            }
        }
    }

    /**
     * Clear all LLMs.txt related cache
     */
    protected function clear_llmstxt_cache()
    {
        $this->cache->destroy('_llmstxt_main');
        $this->cache->destroy('_llmstxt_full');
    }
}