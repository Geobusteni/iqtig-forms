# IQTIG Forms Plugin - Release v1.1.0

**Release Date:** November 2025
**Version:** 1.1.0

---

## Executive Summary

This release integrates the IQTIG Forms WordPress plugin with the Mediakom external API, enabling seamless patient survey access. Users can now log in through the WordPress portal, receive a secure JWT token, and be redirected to the appropriate Mediakom survey. Additionally, users can unsubscribe from survey notifications directly through the portal.

**Key Deliverables:**
- Full API integration with Mediakom authentication and unsubscribe endpoints
- Admin settings page for centralized configuration
- Flexible redirect URL management (global defaults with per-form overrides)
- Survey ID resolution from multiple sources (URL, form settings, global settings)
- JWT token storage for secure handoff to Mediakom

---

## Features Overview

### 1. Login Form Integration
The Login Form block now communicates with the Mediakom authentication API:
- Securely authenticates users with username and password
- Receives and stores JWT tokens for survey access
- Automatically redirects users to Mediakom surveys after successful login
- Handles all API error responses with user-friendly messages

### 2. Unsubscribe Form Integration
The Unsubscribe Form block allows users to opt-out of survey notifications:
- Automatically retrieves Survey ID from URL parameters when available
- Submits unsubscribe requests to Mediakom API
- Provides confirmation and redirects after successful unsubscription

### 3. Global Settings
A new settings page under **WordPress Admin → Settings → IQTIG Forms** provides:
- Centralized API configuration
- Default redirect URLs for both form types
- Fallback Survey ID configuration
- JWT token lifetime settings

### 4. Per-Form Customization
Each form instance can override global settings:
- Custom redirect URLs per form
- Individual Survey ID settings for unsubscribe forms
- Toggle between global and custom configurations

---

## How It Works

### Login Flow

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  User enters    │────▶│  WordPress      │────▶│  Mediakom API   │
│  credentials    │     │  validates &    │     │  authenticates  │
│                 │     │  proxies        │     │  & returns JWT  │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                        │
                                                        ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  User accesses  │◀────│  Mediakom       │◀────│  JWT stored     │
│  correct survey │     │  reads JWT      │     │  in cookie      │
│                 │     │  & redirects    │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

1. User enters username and password on the login form
2. Form submits to WordPress REST API
3. WordPress proxies the request to Mediakom API (`/auth/login`)
4. Mediakom authenticates and returns a JWT token (valid for 4 hours)
5. WordPress stores the JWT in a cookie and returns redirect URL
6. User is redirected to Mediakom
7. Mediakom reads the JWT cookie and directs user to their survey

### Unsubscribe Flow

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  User clicks    │────▶│  Survey ID      │────▶│  WordPress      │
│  unsubscribe    │     │  resolved from  │     │  proxies to     │
│  link           │     │  URL/settings   │     │  Mediakom API   │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                        │
                                                        ▼
                        ┌─────────────────┐     ┌─────────────────┐
                        │                 │     │                 │
                        │  User sees      │◀────│  Mediakom       │
                        │  confirmation   │     │  unsubscribes   │
                        │  & redirected   │     │  user           │
                        └─────────────────┘     └─────────────────┘
```

1. User arrives at unsubscribe page (often via email link with `?surveyId=XXX`)
2. Survey ID is automatically extracted from URL
3. User provides reason and submits form
4. WordPress proxies request to Mediakom API (`/survey/unsubscribe`)
5. User is redirected to confirmation page

### JWT Token Handling

The JWT (JSON Web Token) is a secure, time-limited credential that:
- Identifies the user without exposing login credentials
- Is valid for 4 hours (configurable)
- Is stored in a browser cookie accessible to Mediakom
- Allows Mediakom to redirect users to their specific survey

---

## Configuration Guide

### Accessing Settings

1. Log in to WordPress Admin
2. Navigate to **Settings → IQTIG Forms**

### Available Settings

| Setting | Description | Default |
|---------|-------------|---------|
| API Base URL | The Mediakom API endpoint | `https://api.iqtig.org` |
| JWT Cookie Lifetime | How long the login token remains valid (seconds) | 14400 (4 hours) |
| Login Redirect URL | Where to send users after successful login | (empty - uses home page) |
| Unsubscribe Redirect URL | Where to send users after unsubscribing | (empty - uses home page) |
| Default Survey ID | Fallback Survey ID if not in URL | (empty) |
| Use Default Survey ID | Enable fallback when URL parameter is missing | No |

### Recommended Configuration

1. **API Base URL:** Set this to the production Mediakom API URL provided by the technical team
2. **Login Redirect URL:** Set to the Mediakom survey entry point
3. **Unsubscribe Redirect URL:** Set to a thank-you or confirmation page
4. **JWT Cookie Lifetime:** Keep at 14400 (4 hours) unless instructed otherwise

---

## Block Usage Guide

### Adding a Login Form

1. Edit a page or post in WordPress
2. Click the **+** button to add a block
3. Search for "IQTIG Login Form"
4. Click to add the block

**Block Settings (Sidebar):**
- **Domain:** Optional identifier for the authentication domain
- **Form ID:** Unique identifier for this form instance
- **Use Global Redirect URL:** Toggle ON to use the global setting, OFF for custom
- **Redirect URL:** (Only shown when toggle is OFF) Custom redirect for this form

**Adding Fields:**
1. Click "Add Field" in the block editor
2. Choose field type (Text, Textarea, etc.)
3. Set field name, label, and required status
4. For username: use name "username"
5. For password: use name "password"

### Adding an Unsubscribe Form

1. Edit a page or post in WordPress
2. Click the **+** button to add a block
3. Search for "IQTIG Unsubscribe Form"
4. Click to add the block

**Block Settings (Sidebar):**
- **Domain:** Optional identifier
- **Form ID:** Unique identifier for this form
- **Use Global Redirect URL:** Toggle for global vs custom redirect
- **Redirect URL:** Custom redirect (when toggle is OFF)
- **Survey ID:** Per-form Survey ID override
- **Read Survey ID from URL:** Toggle ON to check URL parameter first

**Survey ID Priority:**
1. URL parameter (`?surveyId=XXX`) - if "Read from URL" is enabled
2. Block's Survey ID setting
3. Global Default Survey ID (if enabled in settings)

---

## API Integration Summary

### Endpoints Used

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/auth/login` | POST | Authenticate user and receive JWT |
| `/survey/unsubscribe` | POST | Unsubscribe user from notifications |

### Error Codes

| Code | Error | User Message |
|------|-------|--------------|
| 404 | PATIENT_NOT_FOUND | The account was not found. Please check your credentials. |
| 403 | SURVEY_UNSUBSCRIBED | You have already unsubscribed from this survey. |
| 403 | SURVEY_DEADLINE | The deadline to complete this survey has passed. |
| 409 | SURVEY_ALREADY_COMPLETED | You have already completed this survey. |
| 409 | ALREADY_UNSUBSCRIBED | You have already unsubscribed from this survey. |
| 500 | INTERNAL_SERVER_ERROR | A technical error occurred. Please try again later. |

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | November 2025 | Initial release with form blocks and cookie persistence |
| 1.1.0 | November 2025 | API integration, global settings, JWT token handling |

### What's New in v1.1.0

- **API Integration:** Login and unsubscribe forms now communicate with Mediakom API
- **Settings Page:** New admin interface for centralized configuration
- **JWT Tokens:** Secure token-based authentication for survey access
- **Flexible Redirects:** Global defaults with per-form override capability
- **Survey ID Resolution:** Automatic extraction from URL parameters
- **Error Handling:** User-friendly error messages for all API responses

---

## Technical Contact

For technical issues or questions about this plugin, please contact:

- **Development Team:** [Contact information to be added]
- **Issue Tracker:** [GitHub repository URL to be added]

---

## Accessibility

This plugin complies with:
- **WCAG 2.2 Level AA** - Web Content Accessibility Guidelines
- **BITV** - German accessibility standards

All forms include:
- Proper label associations
- Keyboard navigation support
- Screen reader compatibility
- Error announcements
- Focus management
