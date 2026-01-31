/**
 * API Endpoints
 * Centralized endpoint definitions for type safety
 */

export const API_ENDPOINTS = {
    // Authentication
    AUTH: {
        LOGIN: '/login',
        LOGOUT: '/logout',
        REGISTER: '/register',
        FORGOT_PASSWORD: '/forgot-password',
        RESET_PASSWORD: '/reset-password',
        VERIFY_EMAIL: '/verify-email'
    },

    // Users & Accounts
    ACCOUNTS: {
        LIST: '/accounts',
        CREATE: '/accounts',
        UPDATE: (id) => `/accounts/${id}`,
        DELETE: (id) => `/accounts/${id}`,
        SHOW: (id) => `/accounts/${id}`
    },

    // Admin Routes
    ADMIN: {
        USERS: {
            LIST: '/admin/users',
            CREATE: '/admin/users',
            UPDATE: (id) => `/admin/users/${id}`,
            DELETE: (id) => `/admin/users/${id}`
        },
        ROLES: {
            LIST: '/admin/roles',
            CREATE: '/admin/roles',
            UPDATE: (id) => `/admin/roles/${id}`,
            DELETE: (id) => `/admin/roles/${id}`
        },
        PERMISSIONS: {
            LIST: '/admin/permissions'
        },
        PLANS: {
            LIST: '/admin/plans',
            CREATE: '/admin/plans',
            UPDATE: (id) => `/admin/plans/${id}`,
            DELETE: (id) => `/admin/plans/${id}`
        },
        SUBSCRIPTIONS: {
            LIST: '/admin/subscriptions',
            CREATE: '/admin/subscriptions',
            DELETE: (id) => `/admin/subscriptions/${id}`
        },
        LOOKUPS: {
            LIST: '/admin/lookups',
            VALUES: {
                CREATE: '/admin/lookups/values',
                UPDATE: (id) => `/admin/lookups/values/${id}`,
                DELETE: (id) => `/admin/lookups/values/${id}`
            }
        },
        CARDS: {
            LIST: '/admin/cards/all',
            GROUPS: {
                LIST: '/admin/cards',
                CREATE: '/admin/cards/groups',
                UPDATE: (id) => `/admin/cards/groups/${id}`,
                DELETE: (id) => `/admin/cards/groups/${id}`,
                CARDS: (groupId) => `/admin/cards/groups/${groupId}/cards`
            }
        },
        CUSTOMERS: {
            LIST: '/admin/customers',
            CREATE: '/admin/customers',
            UPDATE: (id) => `/admin/customers/${id}`,
            DELETE: (id) => `/admin/customers/${id}`,
            SHOW: (id) => `/admin/customers/${id}`
        },
        STUDIOS: {
            LIST: '/admin/studios',
            CREATE: '/admin/studios',
            UPDATE: (id) => `/admin/studios/${id}`,
            DELETE: (id) => `/admin/studios/${id}`
        },
        SCHOOLS: {
            LIST: '/admin/schools',
            CREATE: '/admin/schools',
            UPDATE: (id) => `/admin/schools/${id}`,
            DELETE: (id) => `/admin/schools/${id}`
        }
    },

    // Studio Routes
    STUDIO: {
        PROFILE: {
            SHOW: '/studio/profile',
            UPDATE: '/studio/profile'
        },
        ALBUMS: {
            LIST: '/studio/albums',
            CREATE: '/studio/albums',
            UPDATE: (id) => `/studio/albums/${id}`,
            DELETE: (id) => `/studio/albums/${id}`
        },
        CUSTOMERS: {
            LIST: '/studio/customers',
            CREATE: '/studio/customers',
            UPDATE: (id) => `/studio/customers/${id}`,
            DELETE: (id) => `/studio/customers/${id}`
        },
        CARDS: {
            LIST: '/studio/cards',
            CREATE: '/studio/cards',
            UPDATE: (id) => `/studio/cards/${id}`,
            DELETE: (id) => `/studio/cards/${id}`,
            LINK_ALBUMS: (id) => `/studio/cards/${id}/link-albums`
        },
        STORAGE: {
            LIST: '/studio/storage/libraries',
            CREATE: '/studio/storage/libraries',
            UPDATE: (id) => `/studio/storage/libraries/${id}`,
            DELETE: (id) => `/studio/storage/libraries/${id}`
        },
        PHOTO_REVIEW: {
            PENDING: '/studio/photo-review/pending',
            REVIEW: (id) => `/studio/photo-review/${id}/review`
        }
    },

    // School Routes
    SCHOOL: {
        PROFILE: {
            SHOW: '/school/profile',
            UPDATE: '/school/profile'
        },
        ALBUMS: {
            LIST: '/school/albums',
            SHOW: (id) => `/school/albums/${id}`
        },
        CARDS: {
            LIST: '/school/cards',
            LINK_ALBUMS: (id) => `/school/cards/${id}/link-albums`
        },
        STUDENTS: {
            LIST: '/school/students',
            SHOW: (id) => `/school/students/${id}`
        }
    },

    // Customer Routes
    CUSTOMER: {
        PHOTOS: {
            UPLOAD: '/customer/photos/upload'
        }
    },

    // Common
    CSRF_TOKEN: '/csrf-token',
    PROFILE: '/profile'
};

/**
 * Build URL with query parameters
 * @param {string} endpoint - Base endpoint
 * @param {Object} params - Query parameters
 * @returns {string} - Full URL with params
 */
export function buildUrl(endpoint, params = {}) {
    const url = new URL(endpoint, window.location.origin);
    
    Object.entries(params).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            url.searchParams.append(key, value);
        }
    });
    
    return url.pathname + url.search;
}

/**
 * Get endpoint with ID replacement
 * @param {Function|string} endpoint - Endpoint or function
 * @param {*} id - ID to replace
 * @returns {string} - Final endpoint
 */
export function getEndpoint(endpoint, id = null) {
    if (typeof endpoint === 'function') {
        return endpoint(id);
    }
    return endpoint;
}

export default API_ENDPOINTS;
