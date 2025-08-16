# phpBB LLMs.txt Generator Extension

A phpBB 3.3.x extension that generates dynamic llms.txt files to help Large Language Models (LLMs) understand and interact with your forum.

## Features

- **Dynamic llms.txt Generation**: Provides `/llms.txt` endpoint with forum overview
- **Extended Documentation**: `/llms-full.txt` with comprehensive forum information
- **Markdown Conversion**: Convert forum and topic pages to markdown format (`.md` suffix)
- **Permission-Aware**: Respects forum permissions and only shows public content
- **Configurable Caching**: Reduce server load with intelligent caching
- **Admin Control Panel**: Full configuration options through ACP
- **Event-Based Cache Invalidation**: Automatically updates when content changes

## Requirements

- phpBB 3.3.0 or higher
- PHP 7.2.0 or higher

## Installation

1. Download the extension
2. Copy the `topofgames` folder to `phpBB3/ext/`
3. Navigate to ACP -> Customise -> Extensions
4. Find "LLMs.txt Generator" and click Enable

## Configuration

After installation, configure the extension in:
**ACP -> Extensions -> LLMs.txt Generator Settings**

### Available Settings

- **Enable/Disable**: Turn the feature on/off
- **Custom Header**: Add custom text to the llms.txt output
- **Content Options**:
  - Include forum statistics
  - Include recent announcements
  - Maximum forums/topics to display
- **Cache Settings**:
  - Cache duration for llms.txt (default: 1 hour)
  - Cache duration for llms-full.txt (default: 6 hours)

## Endpoints

Once enabled, your forum will provide:

- `/llms.txt` - Main documentation file with forum structure
- `/llms-full.txt` - Extended documentation with additional details
- `/viewforum.php?f=X.md` - Markdown version of forum pages
- `/viewtopic.php?t=X.md` - Markdown version of topic pages

## Permissions

The extension adds a new user permission:
- `u_view_llmstxt` - Can view llms.txt files

By default, this is granted to registered users. You can modify this in:
**ACP -> Permissions -> User permissions**

## Security

- Only public forums are included (no password-protected forums)
- Respects all existing forum permissions
- No user-specific data is exposed
- Includes robots meta tag to prevent search engine indexing

## Example Output

```markdown
# My Forum

> A community forum for discussions about technology and gaming.

## Forum Statistics
- Total Users: 5,234
- Total Posts: 123,456
- Total Topics: 12,345

## Forum Structure

### Technology
Discussion about computers and software
- Programming & Development
- Hardware & Builds

### Gaming
Everything related to video games
- PC Gaming
- Console Gaming

## Important Links
- [Forum Rules](/rules)
- [FAQ](/faq.php)
- [Search](/search.php)
```

## Cache Invalidation

The cache is automatically cleared when:
- New posts are made (especially announcements)
- Posts are deleted
- Topics are moved
- Forum structure changes
- Board configuration changes

## Support

For issues or questions, please create an issue on GitHub:
https://github.com/topofgames/phpbb-llms

## License

GNU General Public License, version 2 (GPL-2.0)