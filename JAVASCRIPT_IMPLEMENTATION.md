# IQTIG Forms Plugin - JavaScript Implementation

## Overview

Complete JavaScript implementation for the IQTIG Forms WordPress plugin, including shared utilities, reusable components, and two Gutenberg blocks (Login Form and Unsubscribe Form).

## Architecture

### Directory Structure

```
/root/gits/iqtig-plugin/
├── src/
│   ├── utils/
│   │   ├── validation.js          # Field validation utilities
│   │   ├── cookie-manager.js      # Cookie storage management
│   │   └── accessibility.js       # Accessibility helpers
│   └── components/
│       └── FormField.js           # Reusable form field component
├── blocks/
│   ├── login-form/
│   │   ├── block.json            # Block metadata
│   │   ├── index.js              # Block registration
│   │   ├── edit.js               # Editor interface
│   │   ├── save.js               # Save function (returns null)
│   │   ├── view.js               # Frontend JavaScript
│   │   ├── style.scss            # Frontend styles
│   │   └── editor.scss           # Editor styles
│   └── unsubscribe-form/
│       ├── block.json            # Block metadata
│       ├── index.js              # Block registration
│       ├── edit.js               # Editor interface
│       ├── save.js               # Save function (returns null)
│       ├── view.js               # Frontend JavaScript
│       ├── style.scss            # Frontend styles
│       └── editor.scss           # Editor styles
├── .eslintrc.js                  # ESLint configuration
├── package.json                  # Dependencies and scripts
├── postcss.config.js             # PostCSS configuration
└── webpack.config.js             # Webpack configuration
```

## File Descriptions

### Shared Utilities

#### `/root/gits/iqtig-plugin/src/utils/validation.js`
**Purpose**: Form field validation
**Exports**:
- `validateRequired(value)` - Validates required fields (supports string, boolean, array)
- `validateAllFields(fields)` - Validates all fields in a form, returns errors object

**Features**:
- Handles text, checkbox, radio, and select field types
- Returns screen-reader friendly error messages
- Array support for multiple selections

#### `/root/gits/iqtig-plugin/src/utils/cookie-manager.js`
**Purpose**: Cookie storage and retrieval
**Exports**:
- `setCookie(name, value, days=30)` - Sets cookie with expiration
- `getCookie(name)` - Retrieves cookie value
- `setFormData(formId, fieldName, value)` - Stores form field in cookie
- `getFormData(formId)` - Retrieves all form data for a specific form

**Cookie Format**:
- Key: `iqtig_form_{formId}_{fieldName}`
- Value: JSON encoded
- Expiration: 30 days
- Path: `/`
- SameSite: `Lax`

#### `/root/gits/iqtig-plugin/src/utils/accessibility.js`
**Purpose**: Accessibility helpers for WCAG 2.2 compliance
**Exports**:
- `announceError(message)` - Announces errors via ARIA live region
- `focusFirstError(formElement)` - Focuses first invalid field
- `setupKeyboardNavigation(formElement, submitCallback)` - Enables Enter key submission

**Features**:
- Creates hidden ARIA live region for screen reader announcements
- Smooth scrolling to error fields
- Keyboard navigation support (Tab, Enter, Space)

### Components

#### `/root/gits/iqtig-plugin/src/components/FormField.js`
**Purpose**: Reusable form field component
**Props**:
- `type` - Field type (text, textarea, checkbox, radio, select)
- `name` - Field name
- `label` - Field label
- `required` - Whether field is required
- `value` - Field value
- `onChange` - Change handler
- `options` - Options for select/radio fields
- `error` - Error message
- `id` - Field ID (auto-generated if not provided)

**Features**:
- Renders different field types with appropriate HTML
- Proper ARIA attributes (aria-required, aria-invalid, aria-describedby)
- Visual required indicator (*)
- Error message display with role="alert"
- Radio group with proper role="radiogroup"

### Login Form Block

#### `/root/gits/iqtig-plugin/blocks/login-form/block.json`
**Block Name**: `iqtig-forms/login-form`
**Attributes**:
- `domain` (string) - Authentication domain
- `formId` (string) - Unique form identifier
- `redirectUrl` (string) - Post-submission redirect URL
- `fields` (array) - Form fields configuration

#### `/root/gits/iqtig-plugin/blocks/login-form/edit.js`
**Purpose**: Block editor interface
**Features**:
- InspectorControls for form settings (domain, formId, redirectUrl)
- Field manager UI:
  - Add/delete fields
  - Field type selector (text, textarea, checkbox, radio, select)
  - Required toggle
  - Reorder fields (up/down arrows)
  - Options editor for radio/select fields
- Live preview of form in editor
- All text uses `__()` for translations

#### `/root/gits/iqtig-plugin/blocks/login-form/view.js`
**Purpose**: Frontend form functionality
**Features**:
- Restores field values from cookies on load
- Auto-saves field values to cookies on change
- Form validation on submit:
  1. Validates all required fields
  2. Displays accessible errors if invalid
  3. Focuses first error field
  4. Announces errors to screen readers
- Redirects to `redirectUrl` on successful validation
- Keyboard support (Enter to submit, except in textarea)
- Proper error display with ARIA attributes

#### `/root/gits/iqtig-plugin/blocks/login-form/style.scss`
**Purpose**: Frontend styles (WCAG 2.2 compliant)
**Features**:
- 4.5:1 color contrast ratio for text
- 2px focus outlines with high visibility
- Error message styling (red, bold, border)
- Responsive design (mobile-friendly)
- iOS zoom prevention (16px font on mobile)
- High contrast mode support
- Dark mode support
- Reduced motion support
- Accessible form controls (1.25rem checkboxes/radios)

#### `/root/gits/iqtig-plugin/blocks/login-form/editor.scss`
**Purpose**: Editor-specific styles
**Features**:
- Field manager interface styling
- Drag handles for reordering
- Add/delete button styles
- Preview panel styling
- Responsive editor layout

### Unsubscribe Form Block

The unsubscribe form block has the same structure and functionality as the login form block, with the following differences:

#### `/root/gits/iqtig-plugin/blocks/unsubscribe-form/block.json`
**Block Name**: `iqtig-forms/unsubscribe-form`
**Title**: "IQTIG Unsubscribe Form"

All other files (`edit.js`, `view.js`, `style.scss`, `editor.scss`) are identical in functionality to the login form block, with appropriate title and text changes.

## Configuration Files

### `/root/gits/iqtig-plugin/.eslintrc.js`
**Purpose**: ESLint configuration for WordPress coding standards
**Rules**:
- WordPress recommended preset
- No console.log in production
- Accessibility rules (jsx-a11y)
- Code quality rules (no-unused-vars, prefer-const, no-var)
- Formatting rules (indent, quotes, semi)

### `/root/gits/iqtig-plugin/package.json`
**Scripts**:
- `npm run build` - Build production bundle
- `npm run start` - Start development server
- `npm run lint:js` - Lint JavaScript files
- `npm run lint:css` - Lint CSS/SCSS files
- `npm run format:js` - Format JavaScript files

**Dependencies**:
- `@wordpress/block-editor` - Block editor components
- `@wordpress/blocks` - Block registration
- `@wordpress/components` - UI components
- `@wordpress/element` - React wrapper
- `@wordpress/i18n` - Internationalization

**Dev Dependencies**:
- `@wordpress/scripts` - Build tooling (wp-scripts)
- `autoprefixer` - CSS vendor prefixing
- `cssnano` - CSS minification
- `postcss` - CSS processing

### `/root/gits/iqtig-plugin/postcss.config.js`
**Purpose**: PostCSS configuration
**Plugins**:
- `autoprefixer` - Adds vendor prefixes
- `cssnano` - Minifies CSS for production

### `/root/gits/iqtig-plugin/webpack.config.js`
**Purpose**: Webpack configuration for building blocks
**Entry Points**:
- `login-form/index` - Login form editor script
- `login-form/view` - Login form frontend script
- `unsubscribe-form/index` - Unsubscribe form editor script
- `unsubscribe-form/view` - Unsubscribe form frontend script

## Field Types Supported

1. **Text Field** (`type="text"`)
   - Single-line text input
   - Proper label association

2. **Textarea** (`<textarea>`)
   - Multi-line text input
   - 4 rows default height
   - Vertically resizable

3. **Checkbox** (`type="checkbox"`)
   - Boolean value
   - Can be multiple per form

4. **Radio** (`type="radio"`)
   - Single selection from options
   - Proper radio group with role="radiogroup"
   - Options defined in field configuration

5. **Select** (`<select>`)
   - Dropdown selection
   - Options defined in field configuration
   - Includes "-- Select an option --" placeholder

## Accessibility Compliance

### WCAG 2.2 Standards Met

1. **Perceivable**:
   - All fields have visible labels
   - Required fields marked with asterisk (*)
   - Error messages in high contrast (red on light background)
   - 4.5:1 color contrast ratio for text

2. **Operable**:
   - All fields keyboard accessible (Tab navigation)
   - Enter key submits form (except in textarea)
   - Focus visible with 2px outline
   - No keyboard traps

3. **Understandable**:
   - Clear error messages
   - Required field indicators
   - Descriptive button text
   - Screen reader announcements

4. **Robust**:
   - Proper ARIA attributes (aria-required, aria-invalid, aria-describedby)
   - Semantic HTML (label, form, role attributes)
   - Error messages with role="alert"
   - ARIA live regions for announcements

### BITV Compliance

The implementation meets BITV (Barrierefreie-Informationstechnik-Verordnung) requirements for German accessibility standards:

- Form fields properly labeled
- Error messages announced to screen readers
- Keyboard navigation fully supported
- Focus states clearly visible
- High contrast support

## Cookie Storage

### Format
```javascript
// Cookie name format
iqtig_form_{formId}_{fieldName}

// Examples
iqtig_form_login_username = "john.doe"
iqtig_form_login_remember = true
iqtig_form_unsubscribe_email = "user@example.com"
```

### Storage Behavior
- **Auto-save**: Field values saved immediately on change
- **Auto-restore**: Values restored from cookies on page load
- **Expiration**: 30 days from last save
- **Path**: `/` (site-wide)
- **SameSite**: `Lax` (CSRF protection)

## Build Process

### Development
```bash
npm install
npm run start
```

### Production
```bash
npm run build
```

### Linting
```bash
npm run lint
```

## Text Domain

All translatable strings use the text domain `iqtig-forms`:

```javascript
__( 'Submit', 'iqtig-forms' )
```

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11 not supported (uses modern JavaScript)
- Mobile browsers fully supported
- iOS zoom prevention on form fields

## Performance Considerations

- Minimal JavaScript payload (shared utilities reused)
- CSS optimized with cssnano
- No external dependencies (uses WordPress packages)
- Lazy loading of frontend scripts (only loads when block present)

## Security

- No XSS vulnerabilities (proper encoding)
- Cookie values JSON encoded
- SameSite=Lax cookie attribute
- No inline JavaScript
- CSP friendly

## Testing Checklist

- [ ] All fields save to cookies
- [ ] All fields restore from cookies
- [ ] Required field validation works
- [ ] Error messages display correctly
- [ ] Error announcements work with screen reader
- [ ] Focus management on errors works
- [ ] Keyboard navigation works (Tab, Enter, Space)
- [ ] Form redirects on successful validation
- [ ] ESLint passes with no errors
- [ ] Styles build correctly
- [ ] Mobile responsive
- [ ] High contrast mode works
- [ ] Dark mode works
- [ ] Reduced motion respected
