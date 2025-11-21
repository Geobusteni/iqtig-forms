# IQTIG Forms

Custom forms plugin for IQTIG WordPress site with login and unsubscribe functionality.

## Features

- Login form Gutenberg block
- Unsubscribe form Gutenberg block
- Client-side form validation
- Cookie management (30-day expiration, SameSite Lax)
- REST API endpoints for form handling
- Translation-ready
- WordPress VIP coding standards compliant
- PHPStan level 8 compliant

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher

## Installation

1. Clone or download this repository to your WordPress plugins directory
2. Run `composer install` to install PHP dependencies
3. Run `npm install` to install JavaScript dependencies
4. Run `npm run build` to build the block assets
5. Activate the plugin through the WordPress admin panel

## Development

### Code Quality

Run PHP CodeSniffer:
```bash
composer phpcs
```

Run PHPStan:
```bash
composer phpstan
```

Fix automatically fixable issues:
```bash
composer phpcbf
```

### Building Assets

Start development mode with hot reload:
```bash
npm start
```

Build for production:
```bash
npm run build
```

Lint JavaScript:
```bash
npm run lint:js
```

Lint CSS:
```bash
npm run lint:css
```

## Plugin Structure

```
iqtig-forms/
├── assets/
│   ├── css/          # Frontend stylesheets
│   └── js/           # Frontend scripts
├── blocks/
│   ├── login-form/   # Login form block
│   └── unsubscribe-form/  # Unsubscribe form block
├── includes/
│   ├── activation.php      # Plugin activation
│   ├── deactivation.php    # Plugin deactivation
│   ├── ajax-handler.php    # REST API endpoints
│   ├── assets.php          # Asset enqueueing
│   ├── blocks.php          # Block registration
│   └── cookie-handler.php  # Cookie management
├── languages/        # Translation files
├── iqtig-forms.php   # Main plugin file
├── composer.json     # PHP dependencies
└── package.json      # JavaScript dependencies
```

## Coding Standards

- **PHP**: WordPress VIP Go coding standards
- **JavaScript**: WordPress ESLint plugin
- **Code Style**: Procedural PHP with namespacing
- **Security**: Input validation, output escaping, nonce verification
- **Translations**: All strings use text domain 'iqtig-forms'

## Cookie Parameters

Forms set cookies with the following parameters:
- **Expiration**: 30 days
- **Path**: / (entire site)
- **Secure**: true on HTTPS sites
- **HttpOnly**: false (JavaScript access required)
- **SameSite**: Lax

## License

GPL v2 or later
