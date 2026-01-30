# Frontend Architecture Documentation

## Overview

This document describes the new professional JavaScript architecture implemented for the SPA system, with focus on **security**, **maintainability**, and **performance**.

## Directory Structure

```
resources/js/spa/
├── core/                           # Core Framework Layer
│   ├── security/                   # Security utilities
│   │   ├── XssProtection.js       # XSS sanitization
│   │   ├── CsrfProtection.js      # CSRF token management
│   │   ├── InputValidator.js      # Client-side validation
│   │   └── Security.js            # General security utilities
│   ├── api/                        # API layer
│   │   ├── ApiClient.js           # Centralized Axios client
│   │   └── endpoints.js           # API endpoint definitions
│   ├── ui/                         # UI components
│   │   ├── Toast.js               # Toast notifications
│   │   └── Modal.js               # Modal component
│   ├── utils/                      # Utility functions
│   │   ├── dom.js                 # Safe DOM manipulation
│   │   ├── formatters.js          # Date, number formatting
│   │   └── performance.js         # Debounce, throttle
│   └── index.js                    # Core exports
├── modules/                        # Feature modules
│   └── accounts/                   # Accounts module (MVC)
│       ├── controllers/
│       │   └── AccountController.js
│       ├── views/
│       │   └── AccountView.js
│       └── index.js               # Module entry point
├── models/                         # Data models
├── services/                       # API services
├── utils/                          # Legacy utils (bridged to core)
└── pages/                          # Legacy pages (to be refactored)
```

---

## Core Framework

### 1. Security Layer

#### XssProtection.js
Protects against XSS attacks by sanitizing all user inputs before rendering.

**Usage:**
```javascript
import { XssProtection } from '../core/security/XssProtection.js';

// Escape HTML
const safe = XssProtection.escape(userInput);

// Set text content safely
XssProtection.setTextContent(element, userInput);

// Sanitize URL
const safeUrl = XssProtection.sanitizeUrl(url);
```

**Key Methods:**
- `escape(str)`: Escape HTML special characters
- `sanitizeHtml(html)`: Sanitize HTML content
- `sanitizeUrl(url)`: Prevent javascript: and data: schemes
- `setTextContent(element, text)`: Safe text setting

#### CsrfProtection.js
Automatically manages CSRF tokens for all AJAX requests.

**Usage:**
```javascript
import { CsrfProtection } from '../core/security/CsrfProtection.js';

// Get current token
const token = CsrfProtection.getToken();

// Refresh token
await CsrfProtection.refreshToken();

// Auto-attached to all ApiClient requests
```

#### InputValidator.js
Client-side validation with Arabic error messages.

**Usage:**
```javascript
import { InputValidator } from '../core/security/InputValidator.js';

const validator = new InputValidator({
    username: ['required', 'alphanumeric', 'min:3'],
    email: ['required', 'email'],
    password: ['required', 'strong']
});

if (!validator.validate(formData)) {
    const errors = validator.getErrors();
    // Display errors
}
```

**Available Rules:**
- `required`, `email`, `min:n`, `max:n`
- `numeric`, `alphanumeric`, `alpha`
- `url`, `phone`, `strong` (password)
- `confirmed`, `between:min,max`, `in:val1,val2`
- `regex:pattern`

---

### 2. API Layer

#### ApiClient.js
Centralized Axios instance with automatic CSRF injection and error handling.

**Features:**
- ✅ Auto CSRF token injection
- ✅ Request/Response interceptors
- ✅ Auto retry on 419 (CSRF mismatch)
- ✅ Auto redirect on 401 (Unauthorized)
- ✅ Validation error handling (422)
- ✅ Development logging

**Usage:**
```javascript
import ApiClient from '../core/api/ApiClient.js';

// GET request
const response = await ApiClient.get('/api/users');

// POST request
const data = await ApiClient.post('/api/users', userData);

// Upload file
await ApiClient.upload('/api/photos', formData, (progress) => {
    console.log(`${progress}% uploaded`);
});
```

#### endpoints.js
Type-safe API endpoint definitions.

**Usage:**
```javascript
import { API_ENDPOINTS, getEndpoint } from '../core/api/endpoints.js';

// Simple endpoint
const url = API_ENDPOINTS.ACCOUNTS.LIST; // '/accounts'

// Endpoint with ID
const url = getEndpoint(API_ENDPOINTS.ACCOUNTS.UPDATE, 123); // '/accounts/123'

// Build URL with query params
const url = buildUrl(API_ENDPOINTS.ACCOUNTS.LIST, {
    search: 'john',
    status: 'active'
}); // '/accounts?search=john&status=active'
```

---

### 3. UI Components

#### Toast.js
Enhanced toast notification system with queue management.

**Usage:**
```javascript
import { Toast } from '../core/ui/Toast.js';

// Show success toast
Toast.success('تم الحفظ بنجاح');

// Show error toast
Toast.error('حدث خطأ', { duration: 7000 });

// Show custom toast
Toast.show('رسالة مخصصة', 'info', {
    duration: 5000,
    dismissible: true,
    onClose: () => console.log('Toast closed')
});

// Clear all toasts
Toast.clearAll();
```

#### Modal.js
Accessible modal component with keyboard navigation.

**Usage:**
```javascript
import { Modal } from '../core/ui/Modal.js';

const modal = new Modal({
    id: 'my-modal',
    title: 'عنوان النافذة',
    size: 'lg', // sm, md, lg, xl
    onOpen: () => console.log('Modal opened'),
    onClose: () => console.log('Modal closed')
});

modal.setBody('<p>محتوى النافذة</p>');
modal.setFooter('<button>حفظ</button>');
modal.open();

// Quick usage
Modal.show({ title: 'تنبيه', body: 'هل أنت متأكد?' });
```

---

### 4. Utilities

#### DOM.js
Safe DOM manipulation helpers.

**Usage:**
```javascript
import { DOM } from '../core/utils/dom.js';

// Create element safely
const div = DOM.create('div', {
    className: 'my-class',
    dataset: { id: '123' }
}, 'Safe text content');

// Query safely
const element = DOM.query('#my-id');
const elements = DOM.queryAll('.my-class');

// Event delegation (performance)
DOM.delegate(table, 'click', '[data-action="delete"]', (e) => {
    const id = e.target.dataset.id;
    deleteItem(id);
});

// Form helpers
const data = DOM.getFormData(formElement);
DOM.setFormData(formElement, data);
DOM.disableForm(formElement);
```

#### Formatters.js
Arabic-localized formatting utilities.

**Usage:**
```javascript
import { Formatters } from '../core/utils/formatters.js';

// Date formatting
Formatters.date(new Date()); // '٣١/٠١/٢٠٢٦'
Formatters.datetime(new Date()); // '٣١ يناير ٢٠٢٦ - ١٢:٣٠'
Formatters.relativeTime(new Date()); // 'منذ ساعتين'

// Number formatting
Formatters.number(1234567.89, 2); // '١٬٢٣٤٬٥٦٧٫٨٩'
Formatters.currency(99.99); // '٩٩٫٩٩ ريال'
Formatters.percentage(75.5); // '٧٥٫٥٪'
Formatters.fileSize(1024 * 1024); // '١٫٠٠ ميجابايت'

// Text formatting
Formatters.phone('0771234567'); // '٠٧٧١ ٢٣٤ ٥٦٧'
Formatters.truncate('Long text...', 20); // 'Long text...'
```

#### performance.js
Performance optimization utilities.

**Usage:**
```javascript
import { debounce, throttle } from '../core/utils/performance.js';

// Debounce search input
searchInput.addEventListener('input', debounce((e) => {
    performSearch(e.target.value);
}, 300));

// Throttle scroll event
window.addEventListener('scroll', throttle(() => {
    updateScrollPosition();
}, 100));
```

---

## Module Architecture (MVC)

### Example: Accounts Module

```
modules/accounts/
├── controllers/
│   └── AccountController.js    # Business logic
├── views/
│   └── AccountView.js          # DOM rendering (XSS-safe)
└── index.js                    # Module entry point
```

#### AccountController.js
Orchestrates all account management logic.

**Responsibilities:**
- Load data from API
- Filter and search
- Handle CRUD operations
- Validate input
- Coordinate with View

**Key Methods:**
- `loadAccounts()`: Fetch from API
- `filterAndRender()`: Apply filters and render
- `showCreateModal()`: Show create form
- `editAccount(id)`: Edit existing account
- `deleteAccount(id)`: Delete account
- `handleFormSubmit()`: Validate and save

#### AccountView.js
Handles all DOM rendering with XSS protection.

**Responsibilities:**
- Render table rows (XSS-safe)
- Manage modal state
- Populate forms
- Show/hide elements

**Key Methods:**
- `render(accounts)`: Render account table
- `createAccountRow(account)`: Create single row (XSS-safe)
- `openModal(title)`: Open modal
- `closeModal()`: Close and reset
- `populateForm(account)`: Fill form for editing

---

## Migration Guide

### For Developers

#### Old Way (Unsafe):
```javascript
tbody.innerHTML = accounts.map(acc => `
    <tr><td>${acc.name}</td></tr>
`).join('');
```

#### New Way (Safe):
```javascript
accounts.forEach(acc => {
    const row = view.createAccountRow(acc);
    tbody.appendChild(row);
});
```

### For Blade Views

#### Old Way:
```blade
@vite('resources/js/spa/pages/accounts.js')
```

#### New Way:
```blade
@vite('resources/js/spa/modules/accounts/index.js')
```

---

## Security Checklist

- [x] XSS Protection on all user inputs
- [x] CSRF tokens on all POST/PUT/DELETE requests
- [x] Input validation before API calls
- [x] URL sanitization
- [x] Safe DOM manipulation
- [x] Clickjacking prevention
- [x] Auto CSRF refresh
- [x] External link protection

---

## Performance Optimizations

1. **Event Delegation**: One listener for multiple elements
2. **Debouncing**: Reduce search API calls
3. **Queue Management**: Limit simultaneous toasts
4. **Request Interceptors**: Reduce code duplication
5. **Code Splitting**: Load modules on-demand

---

##Future Enhancements

- [ ] Virtual scrolling for large tables
- [ ] Offline support with Service Workers
- [ ] WebSocket integration for real-time updates
- [ ] Advanced form builder component
- [ ] Data grid component
- [ ] Unit and integration tests

---

## Support

For questions or issues, please contact the development team.

**Last Updated**: January 31, 2026
