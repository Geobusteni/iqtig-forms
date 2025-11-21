# IQTIG Forms - Installation Guide

## Overview

IQTIG Forms is a WordPress plugin that provides two customizable Gutenberg blocks (Login Form and Unsubscribe Form) with cookie-based data persistence. This guide will walk you through the installation and setup process.

## Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 7.4 or higher
- **Node.js:** 18.x or higher (for development)
- **Composer:** 2.x (for development)
- **npm:** 9.x or higher (for development)

## Installation Methods

### Method 1: Production Installation (End Users)

1. **Download the plugin:**
   - Download the latest release from the GitHub repository
   - Or clone the repository: `git clone https://github.com/Geobusteni/iqtig-forms.git`

2. **Build the plugin:**
   ```bash
   cd iqtig-forms
   npm install --legacy-peer-deps
   npm run build
   ```

3. **Install in WordPress:**
   - Zip the plugin directory (excluding `node_modules`)
   - Upload via WordPress Admin → Plugins → Add New → Upload Plugin
   - Or copy the directory to `wp-content/plugins/iqtig-forms/`

4. **Activate the plugin:**
   - Go to WordPress Admin → Plugins
   - Find "IQTIG Forms" and click "Activate"

### Method 2: Development Installation

1. **Clone the repository:**
   ```bash
   cd /path/to/wordpress/wp-content/plugins/
   git clone https://github.com/Geobusteni/iqtig-forms.git
   cd iqtig-forms
   ```

2. **Install dependencies:**
   ```bash
   # Install PHP dependencies (for code quality tools)
   composer install

   # Install JavaScript dependencies
   npm install --legacy-peer-deps
   ```

3. **Build the blocks:**
   ```bash
   # For development (watch mode)
   npm start

   # For production
   npm run build
   ```

4. **Activate in WordPress:**
   - Go to WordPress Admin → Plugins
   - Find "IQTIG Forms" and click "Activate"

## Configuration

### Adding Blocks to Pages

1. **Create or edit a page/post** in WordPress
2. **Add a new block** using the "+" button
3. **Search for:**
   - "IQTIG Login Form"
   - "IQTIG Unsubscribe Form"
4. **Configure the block settings** (see Block Configuration below)

### Block Configuration

Each block has the following settings in the Inspector Controls (right sidebar):

#### Form Settings
- **Domain:** The domain for external service integration (e.g., `example.com`)
- **Form ID:** Unique identifier for this form (used in cookie keys, e.g., `login_form_1`)
- **Redirect URL:** URL to redirect users after successful validation (e.g., `https://example.com/external-service`)

#### Field Management
- **Add Field:** Click to add a new field to the form
- **Field Types Available:**
  - Text Field
  - Textarea
  - Checkbox (single or group)
  - Radio Buttons (group)
  - Select Dropdown
- **Field Options:**
  - Field Label
  - Field Name (used in cookie storage)
  - Required (checkbox)
  - Field-specific options (e.g., select options, radio choices)

#### Field Reordering
- Use the up/down arrows to reorder fields
- Drag and drop support in the editor

## Cookie Behavior

### How Cookies Work

When users interact with the forms:

1. **Auto-save:** Field values are saved to cookies immediately on change
2. **Cookie Format:** `iqtig_form_{formId}_{fieldName}`
3. **Cookie Properties:**
   - **Expiration:** 30 days
   - **Path:** `/` (available site-wide)
   - **SameSite:** `Lax`
   - **Secure:** `true` (on HTTPS)
   - **HttpOnly:** `false` (JavaScript needs access)

### Example Cookie Keys

For a login form with `formId = "login_1"`:
- `iqtig_form_login_1_username`
- `iqtig_form_login_1_password`
- `iqtig_form_login_1_remember_me`

### Reading Cookies from External Services

External services on the same domain can read these cookies using JavaScript:

```javascript
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return JSON.parse(decodeURIComponent(parts.pop().split(';').shift()));
    }
    return null;
}

// Get form data
const username = getCookie('iqtig_form_login_1_username');
const password = getCookie('iqtig_form_login_1_password');
```

## Form Submission Flow

1. **User fills out form fields** → Automatically saved to cookies
2. **User clicks submit button** → Client-side validation runs
3. **If validation fails** → Accessible error messages displayed, focus on first error
4. **If validation passes** → User redirected to configured URL
5. **External service** → Reads cookie data from redirected user

**Note:** The form does NOT submit data to the server. It only validates and redirects.

## Validation Rules

- **Required Fields:** Must be filled before submission
- **Field Types:**
  - Text: Non-empty string
  - Textarea: Non-empty string
  - Checkbox: At least one checked (for groups)
  - Radio: One option selected
  - Select: Non-empty selection

## Accessibility Features

The plugin is compliant with WCAG 2.2 AA and BITV standards:

- ✅ All fields have associated `<label>` elements
- ✅ Required fields marked with `aria-required="true"` and visual indicator (*)
- ✅ Error messages linked via `aria-describedby`
- ✅ ARIA live regions for dynamic announcements
- ✅ Full keyboard navigation (Tab, Enter, Space)
- ✅ Focus management on validation errors
- ✅ 4.5:1 color contrast ratio
- ✅ 2px focus outlines
- ✅ Screen reader compatible

## Internationalization

### Default Language
The plugin is in English by default and is translation-ready.

### Adding Translations

1. **Generate POT file:**
   ```bash
   npm run pot
   ```
   This creates `languages/iqtig-forms.pot`

2. **Create language files:**
   - Use Poedit or similar tool to translate `iqtig-forms.pot`
   - Save as `iqtig-forms-{locale}.po` (e.g., `iqtig-forms-de_DE.po`)
   - Generate MO file: `iqtig-forms-{locale}.mo`

3. **Place in languages directory:**
   ```
   wp-content/plugins/iqtig-forms/languages/
   ├── iqtig-forms.pot
   ├── iqtig-forms-de_DE.po
   └── iqtig-forms-de_DE.mo
   ```

### German Translation (Example)

A German translation is planned. To create it:

```bash
cd languages/
# Use Poedit to translate iqtig-forms.pot
# Save as iqtig-forms-de_DE.po
# Generate iqtig-forms-de_DE.mo
```

## Development Workflow

### Quality Assurance

```bash
# Lint JavaScript
npm run lint:js

# Lint CSS
npm run lint:css

# Check PHP standards (WordPress VIP)
composer run-script phpcs

# Auto-fix PHP standards
composer run-script phpcbf

# Run PHPStan (level 8)
composer run-script phpstan

# Lint everything
npm run lint
```

### Building

```bash
# Development build (watch mode)
npm start

# Production build
npm run build

# Create distributable ZIP
npm run plugin-zip
```

## Troubleshooting

### Build Errors

**Problem:** `npm install` fails with memory errors

**Solution:**
```bash
rm -rf node_modules package-lock.json
NODE_OPTIONS="--max-old-space-size=4096" npm install --legacy-peer-deps
```

**Problem:** `npm run build` fails

**Solution:**
```bash
# Clear build directory
rm -rf build/
npm run build
```

### WordPress Errors

**Problem:** Blocks don't appear in editor

**Solution:**
1. Ensure plugin is activated
2. Check that `build/` directory exists with compiled assets
3. Clear WordPress cache
4. Try rebuilding: `npm run build`

**Problem:** Cookies not saving

**Solution:**
1. Check that Form ID is set in block settings
2. Open browser DevTools → Application → Cookies
3. Verify cookies are being set with correct path (`/`)
4. Ensure site is using HTTPS for Secure cookies

### PHPCS Errors

**Problem:** PHPCS standards not found

**Solution:**
```bash
./vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs,vendor/automattic/vipwpcs,vendor/phpcsstandards/phpcsextra
```

**Problem:** PHPStan errors about WordPress functions

**Solution:** These are expected. PHPStan doesn't recognize WordPress functions without WordPress loaded. The code is correct.

## Support

- **GitHub Issues:** https://github.com/Geobusteni/iqtig-forms/issues
- **Documentation:** See `.claudemd` in repository root for project guidelines

## Security Considerations

- All input is sanitized using `sanitize_text_field()` or `sanitize_email()`
- All output is escaped using `esc_html()` or `esc_attr()`
- REST API endpoints use nonce verification
- Cookie values are JSON encoded to prevent injection
- No file uploads to avoid security risks

## License

GPL v2 or later

## Credits

Built with WordPress Block Editor (@wordpress/scripts), following WordPress VIP coding standards and WCAG 2.2/BITV accessibility guidelines.
