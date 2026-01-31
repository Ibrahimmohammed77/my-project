import './bootstrap';

// /**
//  * Main Application Entry Point
//  * Enhanced with better structure and error handling
//  */

// import { initCore } from './spa/core/index.js';
// import { setupRoutes } from './spa/core/router.js';
// import { initComponents } from './spa/core/components.js';

// class AlbumsApp {
//     constructor() {
//         this.modules = new Map();
//         this.currentModule = null;
//         this.isInitialized = false;
//     }

//     /**
//      * Initialize the application
//      */
//     async init() {
//         try {
//             console.log('[App] Initializing Albums Application...');

//             // Initialize core framework
//             initCore();

//             // Initialize components
//             await initComponents();

//             // Setup routes
//             setupRoutes();

//             // Load initial module based on current URL
//             await this.loadModuleForCurrentRoute();

//             this.isInitialized = true;
//             console.log('[App] Application initialized successfully');

//             this.dispatchEvent('app:ready');
//         } catch (error) {
//             console.error('[App] Initialization failed:', error);
//             this.showErrorScreen('فشل تحميل التطبيق');
//         }
//     }

//     /**
//      * Register a module
//      * @param {string} name - Module name
//      * @param {Object} module - Module object
//      */
//     registerModule(name, module) {
//         if (this.modules.has(name)) {
//             console.warn(`[App] Module "${name}" already registered`);
//             return;
//         }

//         this.modules.set(name, module);
//         console.log(`[App] Module "${name}" registered`);
//     }

//     /**
//      * Load a module by name
//      * @param {string} moduleName - Module name
//      * @param {Object} params - Module parameters
//      */
//     async loadModule(moduleName, params = {}) {
//         try {
//             if (this.currentModule?.name === moduleName && this.currentModule.params === params) {
//                 return;
//             }

//             // Unload current module
//             await this.unloadCurrentModule();

//             // Get module
//             const module = this.modules.get(moduleName);
//             if (!module) {
//                 throw new Error(`Module "${moduleName}" not found`);
//             }

//             // Show loading state
//             this.showLoading();

//             // Initialize module
//             await module.init(params);

//             // Update current module
//             this.currentModule = { name: moduleName, params, instance: module };

//             // Hide loading
//             this.hideLoading();

//             // Dispatch event
//             this.dispatchEvent('module:loaded', { moduleName, params });

//             console.log(`[App] Module "${moduleName}" loaded successfully`);
//         } catch (error) {
//             console.error(`[App] Failed to load module "${moduleName}":`, error);
//             this.showErrorScreen(`فشل تحميل الوحدة: ${moduleName}`);
//         }
//     }

//     /**
//      * Unload current module
//      */
//     async unloadCurrentModule() {
//         if (!this.currentModule) return;

//         try {
//             const { name, instance } = this.currentModule;

//             if (typeof instance.destroy === 'function') {
//                 await instance.destroy();
//             }

//             this.dispatchEvent('module:unloaded', { moduleName: name });
//             console.log(`[App] Module "${name}" unloaded`);
//         } catch (error) {
//             console.error(`[App] Error unloading module:`, error);
//         }

//         this.currentModule = null;
//     }

//     /**
//      * Load module based on current route
//      */
//     async loadModuleForCurrentRoute() {
//         const path = window.location.pathname;
//         const route = this.findRouteForPath(path);

//         if (route) {
//             await this.loadModule(route.module, route.params);
//         } else {
//             // Default to dashboard or show 404
//             await this.loadModule('dashboard');
//         }
//     }

//     /**
//      * Find route for path
//      * @param {string} path - URL path
//      * @returns {Object|null} - Route object
//      */
//     findRouteForPath(path) {
//         const routes = this.getRoutes();

//         for (const route of routes) {
//             const match = this.matchRoute(route.path, path);
//             if (match) {
//                 return {
//                     ...route,
//                     params: match.params
//                 };
//             }
//         }

//         return null;
//     }

//     /**
//      * Match route pattern with path
//      * @param {string} pattern - Route pattern
//      * @param {string} path - URL path
//      * @returns {Object|null} - Match result
//      */
//     matchRoute(pattern, path) {
//         // Convert pattern to regex
//         const regex = new RegExp(
//             '^' + pattern
//                 .replace(/\//g, '\\/')
//                 .replace(/:\w+/g, '([^\\/]+)') + '$'
//         );

//         const match = path.match(regex);
//         if (!match) return null;

//         const paramNames = [...pattern.matchAll(/:(\w+)/g)].map(m => m[1]);
//         const params = {};

//         paramNames.forEach((name, index) => {
//             params[name] = match[index + 1];
//         });

//         return { params };
//     }

//     /**
//      * Get application routes
//      * @returns {Array} - Routes array
//      */
//     getRoutes() {
//         return [
//             // Admin Routes
//             { path: '/admin/accounts', module: 'accounts' },
//             { path: '/admin/accounts/:id/edit', module: 'accounts.edit' },
//             { path: '/admin/schools', module: 'schools' },
//             { path: '/admin/schools/:id/edit', module: 'schools.edit' },
//             { path: '/admin/studios', module: 'studios' },
//             { path: '/admin/studios/:id/edit', module: 'studios.edit' },
//             { path: '/admin/roles', module: 'roles' },
//             { path: '/admin/permissions', module: 'permissions' },
//             { path: '/admin/plans', module: 'plans' },
//             { path: '/admin/lookups', module: 'lookups' },
//             { path: '/admin/cards', module: 'cards' },
//             { path: '/admin/subscriptions', module: 'subscriptions' },
//             { path: '/admin/subscribers', module: 'subscribers' },

//             // Studio Routes
//             { path: '/studio/albums', module: 'studio.albums' },
//             { path: '/studio/customers', module: 'studio.customers' },
//             { path: '/studio/cards', module: 'studio.cards' },
//             { path: '/studio/storage', module: 'studio.storage' },
//             { path: '/studio/photo-review', module: 'studio.photo-review' },
//             { path: '/studio/profile', module: 'studio.profile' },

//             // School Routes
//             { path: '/school/albums', module: 'school.albums' },
//             { path: '/school/students', module: 'school.students' },
//             { path: '/school/cards', module: 'school.cards' },
//             { path: '/school/profile', module: 'school.profile' },

//             // Dashboard
//             { path: '/dashboard', module: 'dashboard' },
//             { path: '/', module: 'dashboard' }
//         ];
//     }

//     /**
//      * Show loading indicator
//      */
//     showLoading() {
//         const loadingEl = document.getElementById('app-loading');
//         if (loadingEl) {
//             loadingEl.classList.remove('hidden');
//             return;
//         }

//         const loading = document.createElement('div');
//         loading.id = 'app-loading';
//         loading.className = 'fixed inset-0 z-50 bg-white/80 backdrop-blur-sm flex items-center justify-center';
//         loading.innerHTML = `
//             <div class="text-center">
//                 <div class="inline-flex relative">
//                     <div class="absolute inset-0 bg-blue-500 opacity-20 rounded-full animate-ping"></div>
//                     <div class="relative bg-white p-4 rounded-full shadow-xl border border-gray-100">
//                         <i class="fas fa-circle-notch fa-spin text-2xl text-blue-500"></i>
//                     </div>
//                 </div>
//                 <p class="mt-4 text-sm font-medium text-gray-500 animate-pulse">جاري تحميل التطبيق...</p>
//             </div>
//         `;

//         document.body.appendChild(loading);
//     }

//     /**
//      * Hide loading indicator
//      */
//     hideLoading() {
//         const loadingEl = document.getElementById('app-loading');
//         if (loadingEl) {
//             loadingEl.classList.add('hidden');
//             setTimeout(() => loadingEl.remove(), 300);
//         }
//     }

//     /**
//      * Show error screen
//      * @param {string} message - Error message
//      */
//     showErrorScreen(message) {
//         const appContainer = document.getElementById('app');
//         if (!appContainer) return;

//         appContainer.innerHTML = `
//             <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
//                 <div class="text-center max-w-md">
//                     <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-dashed border-red-200">
//                         <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
//                     </div>
//                     <h1 class="text-2xl font-bold text-gray-900 mb-2">حدث خطأ</h1>
//                     <p class="text-gray-600 mb-6">${message}</p>
//                     <button onclick="window.location.reload()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
//                         <i class="fas fa-redo mr-2"></i>
//                         إعادة تحميل الصفحة
//                     </button>
//                 </div>
//             </div>
//         `;
//     }

//     /**
//      * Dispatch custom event
//      * @param {string} eventName - Event name
//      * @param {Object} detail - Event detail
//      */
//     dispatchEvent(eventName, detail = {}) {
//         const event = new CustomEvent(eventName, {
//             bubbles: true,
//             detail
//         });
//         document.dispatchEvent(event);
//     }

//     /**
//      * Listen for events
//      * @param {string} eventName - Event name
//      * @param {Function} handler - Event handler
//      */
//     on(eventName, handler) {
//         document.addEventListener(eventName, handler);
//         return () => document.removeEventListener(eventName, handler);
//     }

//     /**
//      * Get application state
//      * @returns {Object} - Application state
//      */
//     getState() {
//         return {
//             initialized: this.isInitialized,
//             currentModule: this.currentModule,
//             modules: Array.from(this.modules.keys())
//         };
//     }

//     /**
//      * Navigate to URL
//      * @param {string} url - URL to navigate to
//      * @param {Object} options - Navigation options
//      */
//     navigate(url, options = {}) {
//         const { replace = false, state = {} } = options;

//         if (replace) {
//             window.history.replaceState(state, '', url);
//         } else {
//             window.history.pushState(state, '', url);
//         }

//         this.loadModuleForCurrentRoute();
//     }

//     /**
//      * Destroy application
//      */
//     async destroy() {
//         await this.unloadCurrentModule();

//         this.modules.clear();
//         this.currentModule = null;
//         this.isInitialized = false;

//         console.log('[App] Application destroyed');
//     }
// }

// // Create and export global instance
// const app = new AlbumsApp();

// // Make it globally accessible
// if (typeof window !== 'undefined') {
//     window.AlbumsApp = app;
// }

// // Initialize on DOM ready
// if (document.readyState === 'loading') {
//     document.addEventListener('DOMContentLoaded', () => app.init());
// } else {
//     app.init();
// }

// export default app;
