import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
// /**
//  * Bootstrap
//  * Application bootstrap and configuration
//  */

// import app from './app.js';
// import { initCore } from './spa/core/index.js';
// import { setupRoutes } from './spa/core/router.js';
// import { initComponents } from './spa/core/components.js';
// import { serviceFactory } from './spa/core/services.js';
// import { appState } from './spa/core/state.js';

// // Configuration
// const CONFIG = {
//     // API Configuration
//     API: {
//         BASE_URL: '/api',
//         TIMEOUT: 30000,
//         RETRY_ATTEMPTS: 3
//     },

//     // Application Configuration
//     APP: {
//         NAME: 'منصة صوركم',
//         VERSION: '1.0.0',
//         ENV: process.env.NODE_ENV || 'development',
//         DEBUG: process.env.NODE_ENV === 'development'
//     },

//     // Storage Configuration
//     STORAGE: {
//         PREFIX: 'albums_',
//         VERSION: '1.0'
//     },

//     // Security Configuration
//     SECURITY: {
//         CSRF_ENABLED: true,
//         XSS_PROTECTION: true,
//         CORS_ENABLED: true
//     }
// };

// // Make config globally available
// window.CONFIG = CONFIG;

// /**
//  * Initialize application
//  */
// async function initialize() {
//     try {
//         console.group('[Bootstrap] Initializing Application');

//         // Set up error handling
//         setupErrorHandling();

//         // Set up performance monitoring
//         setupPerformanceMonitoring();

//         // Initialize core framework
//         initCore();

//         // Initialize state
//         initializeState();

//         // Initialize services
//         initializeServices();

//         // Initialize components
//         await initComponents();

//         // Setup routes
//         setupRoutes();

//         // Initialize application
//         await app.init();

//         console.groupEnd();
//         console.log('[Bootstrap] Application ready');

//     } catch (error) {
//         console.error('[Bootstrap] Initialization failed:', error);
//         showBootstrapError(error);
//     }
// }

// /**
//  * Set up global error handling
//  */
// function setupErrorHandling() {
//     // Window error handler
//     window.onerror = function(message, source, lineno, colno, error) {
//         console.error('[Global Error]', { message, source, lineno, colno, error });
//         return true;
//     };

//     // Unhandled rejection handler
//     window.onunhandledrejection = function(event) {
//         console.error('[Unhandled Rejection]', event.reason);
//         event.preventDefault();
//     };

//     // Console error wrapper
//     const originalError = console.error;
//     console.error = function(...args) {
//         originalError.apply(console, args);

//         // Send to error tracking service in production
//         if (CONFIG.APP.ENV === 'production') {
//             // Implement error tracking
//         }
//     };
// }

// /**
//  * Set up performance monitoring
//  */
// function setupPerformanceMonitoring() {
//     if (CONFIG.APP.DEBUG) {
//         // Log performance marks
//         const originalMark = performance.mark;
//         performance.mark = function(name) {
//             console.debug(`[Performance] Mark: ${name}`);
//             return originalMark.call(performance, name);
//         };

//         // Log performance measures
//         const originalMeasure = performance.measure;
//         performance.measure = function(name, startMark, endMark) {
//             console.debug(`[Performance] Measure: ${name}`);
//             const measure = originalMeasure.call(performance, name, startMark, endMark);
//             console.debug(`[Performance] Duration: ${measure.duration}ms`);
//             return measure;
//         };
//     }

//     // Monitor long tasks
//     if ('PerformanceObserver' in window) {
//         const observer = new PerformanceObserver((list) => {
//             for (const entry of list.getEntries()) {
//                 if (entry.duration > 50) { // 50ms threshold
//                     console.warn('[Performance] Long task detected:', entry);
//                 }
//             }
//         });

//         observer.observe({ entryTypes: ['longtask'] });
//     }
// }

// /**
//  * Initialize application state
//  */
// function initializeState() {
//     // Load persisted state
//     appState.persist('albums_app_state', [
//         'user',
//         'token',
//         'permissions',
//         'sidebarOpen',
//         'theme'
//     ]);

//     // Initialize from session storage
//     const sessionData = sessionStorage.getItem('albums_session');
//     if (sessionData) {
//         try {
//             const data = JSON.parse(sessionData);
//             appState.setState(data, 'LOAD_FROM_SESSION');
//         } catch (error) {
//             console.error('[Bootstrap] Failed to load session:', error);
//         }
//     }

//     // Save session on page unload
//     window.addEventListener('beforeunload', () => {
//         const state = appState.getState();
//         const sessionState = {
//             user: state.user,
//             token: state.token,
//             permissions: state.permissions
//         };
//         sessionStorage.setItem('albums_session', JSON.stringify(sessionState));
//     });
// }

// /**
//  * Initialize services
//  */
// function initializeServices() {
//     // Configure services
//     serviceFactory.create('accounts', {
//         apiConfig: {
//             baseURL: CONFIG.API.BASE_URL,
//             timeout: CONFIG.API.TIMEOUT
//         },
//         cacheEnabled: true,
//         cacheTTL: 2 * 60 * 1000
//     });

//     serviceFactory.create('schools', {
//         apiConfig: {
//             baseURL: CONFIG.API.BASE_URL,
//             timeout: CONFIG.API.TIMEOUT
//         },
//         cacheEnabled: true,
//         cacheTTL: 2 * 60 * 1000
//     });

//     serviceFactory.create('studios', {
//         apiConfig: {
//             baseURL: CONFIG.API.BASE_URL,
//             timeout: CONFIG.API.TIMEOUT
//         },
//         cacheEnabled: true,
//         cacheTTL: 2 * 60 * 1000
//     });

//     console.log('[Bootstrap] Services initialized');
// }

// /**
//  * Show bootstrap error
//  * @param {Error} error - Error object
//  */
// function showBootstrapError(error) {
//     document.body.innerHTML = `
//         <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
//             <div class="text-center max-w-md">
//                 <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-dashed border-red-200">
//                     <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
//                 </div>
//                 <h1 class="text-2xl font-bold text-gray-900 mb-2">فشل تحميل التطبيق</h1>
//                 <p class="text-gray-600 mb-4">عذراً، حدث خطأ أثناء تحميل التطبيق.</p>
//                 ${CONFIG.APP.DEBUG ? `
//                     <div class="bg-gray-100 p-3 rounded-lg text-sm text-gray-700 text-right mb-4">
//                         <strong>Error:</strong> ${error.message}
//                     </div>
//                 ` : ''}
//                 <div class="flex flex-col sm:flex-row gap-3 justify-center">
//                     <button onclick="window.location.reload()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
//                         <i class="fas fa-redo mr-2"></i>
//                         إعادة التحميل
//                     </button>
//                     <button onclick="window.location.href = '/'" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
//                         <i class="fas fa-home mr-2"></i>
//                         الصفحة الرئيسية
//                     </button>
//                 </div>
//             </div>
//         </div>
//     `;
// }

// /**
//  * Load polyfills for older browsers
//  */
// function loadPolyfills() {
//     // Promise polyfill
//     if (typeof Promise === 'undefined') {
//         import('promise-polyfill').then(() => {
//             console.log('[Bootstrap] Promise polyfill loaded');
//         });
//     }

//     // Fetch polyfill
//     if (typeof fetch === 'undefined') {
//         import('whatwg-fetch').then(() => {
//             console.log('[Bootstrap] Fetch polyfill loaded');
//         });
//     }

//     // Intersection Observer polyfill
//     if (!('IntersectionObserver' in window)) {
//         import('intersection-observer').then(() => {
//             console.log('[Bootstrap] IntersectionObserver polyfill loaded');
//         });
//     }
// }

// /**
//  * Initialize analytics
//  */
// function initializeAnalytics() {
//     if (CONFIG.APP.ENV === 'production') {
//         // Initialize analytics tools
//         console.log('[Bootstrap] Analytics initialized');
//     }
// }

// /**
//  * Initialize internationalization
//  */
// function initializeI18n() {
//     // Set Arabic as default language
//     document.documentElement.lang = 'ar';
//     document.documentElement.dir = 'rtl';

//     // Load translations
//     const savedLang = localStorage.getItem('albums_lang') || 'ar';
//     loadTranslations(savedLang);
// }

// /**
//  * Load translations
//  * @param {string} lang - Language code
//  */
// async function loadTranslations(lang) {
//     try {
//         const response = await fetch(`/translations/${lang}.json`);
//         if (response.ok) {
//             const translations = await response.json();
//             window.TRANSLATIONS = translations;
//             console.log(`[Bootstrap] Translations loaded: ${lang}`);
//         }
//     } catch (error) {
//         console.warn('[Bootstrap] Failed to load translations:', error);
//     }
// }

// // Start initialization when DOM is ready
// if (document.readyState === 'loading') {
//     document.addEventListener('DOMContentLoaded', initialize);
// } else {
//     initialize();
// }

// // Load polyfills for older browsers
// loadPolyfills();

// // Initialize analytics
// initializeAnalytics();

// // Initialize internationalization
// initializeI18n();

// // Export for testing
// export { initialize, CONFIG };
