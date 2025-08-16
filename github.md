# phpBB LLMs.txt Generator Extension

A phpBB 3.3+ extension that generates dynamic llms.txt files for Large Language Model integration, following the September 2024 llms.txt specification.

## 📋 Overview

This extension automatically generates machine-readable documentation of your phpBB forum in the standardized llms.txt format, making your forum content easily accessible to Large Language Models (LLMs) for training, analysis, and AI-powered assistance.

## ✨ Features

### Core Functionality
- **Dynamic llms.txt Generation**: Creates real-time documentation from your forum structure
- **Extended Documentation**: Provides both basic (`/llms.txt`) and detailed (`/llms-full.txt`) endpoints
- **Markdown Export**: Convert forum content to markdown format for easy consumption
- **Caching System**: Configurable caching for optimal performance
- **Permission Control**: Role-based access control for llms.txt files

### Generated Content
- Forum statistics (users, posts, topics, files)
- Forum structure and categories
- Recent announcements and popular topics
- Custom header information
- Metadata about the forum software and configuration

### Additional Endpoints
- `/llms.txt` - Basic forum information
- `/llms-full.txt` - Extended forum documentation  
- `/viewforum.php.md` - Forum content as markdown
- `/viewtopic.php.md` - Topic content as markdown

## 🚀 Installation

### Requirements
- phpBB 3.3.0 or higher
- PHP 7.2.0 or higher
- MySQL 5.7+ / PostgreSQL 9.3+ / SQLite 3.25+

### Installation Steps

1. **Download the Extension**
   ```bash
   cd /path/to/phpbb/ext
   git clone https://github.com/topofgames/phpbb-llms.git topofgames/phpbb_llms
   ```

2. **Enable via Admin Control Panel**
   - Login to your phpBB Admin Control Panel
   - Navigate to **Customise** → **Extensions**
   - Find "LLMs.txt Generator" and click **Enable**

3. **Enable via Command Line** (Alternative)
   ```bash
   cd /path/to/phpbb
   php bin/phpbbcli.php extension:enable topofgames/phpbb_llms
   php bin/phpbbcli.php cache:purge
   ```

## ⚙️ Configuration

### Admin Control Panel Settings

Navigate to **ACP** → **Extensions** → **LLMs.txt Generator** → **Settings**

#### General Settings
- **Enable LLMs.txt**: Toggle the extension on/off
- **Custom Header Text**: Optional text to include at the top of llms.txt files

#### Content Settings
- **Include Forum Statistics**: Show user/post/topic counts
- **Include Recent Announcements**: Display recent global and forum announcements
- **Maximum Forums**: Limit number of forums in structure (1-100)
- **Maximum Popular Topics**: Limit topics in llms-full.txt (1-50)
- **Maximum Announcements**: Limit recent announcements (0-20)

#### Cache Settings
- **Cache Time for llms.txt**: Cache duration in seconds (0 = disabled)
- **Cache Time for llms-full.txt**: Cache duration for extended file (0 = disabled)

### Permissions

Set user permissions under **ACP** → **Permissions** → **Group Permissions**:
- **Can view llms.txt files**: Controls access to all llms.txt endpoints

## 📖 Usage

### Accessing LLMs.txt Files

Once enabled, your forum will provide the following endpoints:

```
https://yourforum.com/llms.txt
https://yourforum.com/llms-full.txt
```

### Sample Output

**Basic llms.txt Example:**
```
# Your Forum Name

> A description of your forum community

## Forum Statistics
- Total Users: 1,234
- Total Posts: 45,678
- Total Topics: 8,901
- Total Files: 123

## Forum Structure

### General Discussion
  - **Announcements**
    Important updates and news
  - **General Chat**
    General discussion topics
```

**Extended llms-full.txt includes:**
- Detailed forum hierarchy
- Popular topics with excerpts
- Recent announcements with content
- Moderation information
- Custom categories and permissions

### Markdown Export

Export forum content as markdown:
```
https://yourforum.com/viewforum.php.md?f=1
https://yourforum.com/viewtopic.php.md?t=1
```

### API Integration

The extension provides clean, cacheable endpoints perfect for:
- LLM training data collection
- Forum content analysis
- Documentation generation
- Search engine optimization
- API integrations

## 🔧 Technical Specifications

### Architecture
- **Namespace**: `topofgames\phpbb_llms`
- **Service Container**: Full Symfony DI integration
- **Event System**: Automatic cache invalidation on content changes
- **Routing**: Custom routing for clean URLs
- **Templating**: Twig-based output formatting

### File Structure
```
ext/topofgames/phpbb_llms/
├── composer.json              # Extension metadata
├── ext.php                    # Extension base class
├── config/
│   ├── services.yml          # Service definitions
│   └── routing.yml           # URL routing
├── controller/
│   ├── main_controller.php   # Main llms.txt controller
│   └── markdown_controller.php # Markdown export controller
├── service/
│   ├── llmstxt_generator.php # Core generation service
│   └── markdown_converter.php # Markdown conversion service
├── acp/                      # Admin Control Panel
├── language/                 # Multi-language support
├── migrations/               # Database schema
└── event/                    # Event listeners
```

### Database Tables

The extension adds configuration values to existing phpBB tables:
- `phpbb_config`: Extension settings
- `phpbb_ext`: Extension registration
- `phpbb_modules`: ACP module registration

No additional database tables are created.

### Performance

- **Caching**: Redis/File/Database caching support
- **Event-driven**: Cache invalidation on content changes
- **Optimized Queries**: Minimal database impact
- **Clean URLs**: SEO-friendly endpoint structure

## 🔄 Cache Management

### Automatic Cache Invalidation

The extension automatically clears cache when:
- New posts or topics are created
- Forum structure changes
- Board configuration updates
- Content is modified or deleted

### Manual Cache Control

```bash
# Clear all phpBB cache
php bin/phpbbcli.php cache:purge

# Clear extension-specific cache (programmatically)
$cache->destroy('_llmstxt_main');
$cache->destroy('_llmstxt_full');
```

## 🛠️ Development

### Extending the Extension

#### Custom Content Providers

Create custom content for llms.txt files:

```php
namespace your\extension\service;

class custom_content_provider
{
    public function get_custom_data()
    {
        return [
            'section_title' => 'Custom Data',
            'content' => 'Your custom content here'
        ];
    }
}
```

#### Event Listeners

Hook into content generation:

```php
namespace your\extension\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class custom_listener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'topofgames.phpbb_llms.generate_content' => 'modify_content',
        ];
    }
    
    public function modify_content($event)
    {
        $content = $event['content'];
        // Modify content here
        $event['content'] = $content;
    }
}
```

### Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## 📝 LLMs.txt Specification

This extension follows the official [llms.txt specification](https://llmstxt.org/) published in September 2024:

### Format Requirements
- Plain text format with Markdown formatting
- UTF-8 encoding
- Maximum recommended size: 100KB for basic, 1MB for extended
- Structured sections with clear headings
- Machine-readable metadata

### Standard Sections
1. **Title and Description**
2. **Statistics and Metadata**
3. **Structure Information**
4. **Content Samples**
5. **Access Information**

## 🔒 Security

### Access Control
- Permission-based access to llms.txt files
- Admin-configurable content inclusion
- Rate limiting via phpBB's built-in mechanisms
- Clean output sanitization

### Data Privacy
- Respects forum privacy settings
- Excludes private forums from public endpoints
- Honors user permission levels
- Configurable content filtering

## 🐛 Troubleshooting

### Common Issues

**Extension not visible in Extensions Manager:**
```bash
# Check directory structure matches namespace
ls ext/topofgames/phpbb_llms/
# Clear cache
php bin/phpbbcli.php cache:purge
```

**Fatal error on activation:**
```bash
# Disable and purge extension
php bin/phpbbcli.php extension:disable topofgames/phpbb_llms
php bin/phpbbcli.php extension:purge topofgames/phpbb_llms
# Re-enable
php bin/phpbbcli.php extension:enable topofgames/phpbb_llms
```

**Empty or missing content:**
- Check permissions in ACP → Permissions
- Verify extension is enabled in settings
- Clear cache and regenerate content

**Language keys not translated:**
- Ensure language files are present in `/language/en/`
- Clear template cache
- Check file permissions

### Debug Mode

Enable debug mode in `config.php`:
```php
@define('DEBUG', true);
@define('DEBUG_CONTAINER', true);
```

### Logs

Check phpBB error logs:
- `/cache/production/logs/`
- Server error logs
- Browser developer console

## 📄 License

This extension is licensed under the [GNU General Public License v2.0](https://www.gnu.org/licenses/gpl-2.0.html).

## 🤝 Support

- **GitHub Issues**: [Report bugs and request features](https://github.com/topofgames/phpbb-llms/issues)
- **phpBB Community**: [Extension support forum](https://www.phpbb.com/community/)
- **Documentation**: [Official llms.txt specification](https://llmstxt.org/)

## 📊 Statistics

- **Version**: 1.0.0
- **Release Date**: 2025
- **Compatibility**: phpBB 3.3.0+
- **Language Support**: English (extensible)
- **Dependencies**: Core phpBB only

---

**Made with ❤️ for the phpBB and AI communities**

*This extension bridges the gap between traditional forum software and modern AI systems, enabling seamless integration and content accessibility for Large Language Models.*