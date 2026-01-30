import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                
                // Core & Modules
                'resources/js/spa/core/index.js',
                'resources/js/spa/modules/accounts/index.js',
                'resources/js/spa/modules/roles/index.js',
                'resources/js/spa/modules/permissions/index.js',
                'resources/js/spa/modules/plans/index.js',
                'resources/js/spa/modules/customers/index.js',
                'resources/js/spa/modules/lookups/index.js',
                'resources/js/spa/modules/cards/index.js',
                
                // Legacy Pages (to be refactored)
                'resources/js/spa/pages/studios.js',
                'resources/js/spa/pages/schools.js',
                // 'resources/js/spa/pages/permissions.js', // REFACTORED
                // 'resources/js/spa/pages/roles.js', // REFACTORED
                // 'resources/js/spa/pages/customers.js', // REFACTORED
                'resources/js/spa/pages/subscribers.js',
                // 'resources/js/spa/pages/lookups.js', // REFACTORED
                // 'resources/js/spa/pages/plans.js', // REFACTORED
                // 'resources/js/spa/pages/cards.js', // REFACTORED
                // 'resources/js/spa/pages/group-cards.js', // REFACTORED
                'resources/js/spa/pages/subscriptions.js',
                
                // Studio
                'resources/js/spa/pages/studio-albums.js',
                'resources/js/spa/pages/studio-customers.js',
                'resources/js/spa/pages/studio-profile.js',
                'resources/js/spa/pages/studio-storage.js',
                'resources/js/spa/pages/studio-photo-review.js',
                'resources/js/spa/pages/studio-cards.js',
                'resources/js/spa/pages/studio-card-detail.js',
                
                // School
                'resources/js/spa/pages/school-albums.js',
                'resources/js/spa/pages/school-cards.js',
                'resources/js/spa/pages/school-students.js',
                'resources/js/spa/pages/school-profile.js',
                
                // Auth
                'resources/js/auth/pages/register.js',
                'resources/js/auth/pages/login.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
