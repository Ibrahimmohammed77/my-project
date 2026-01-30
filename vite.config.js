import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/spa/pages/studios.js',
                'resources/js/spa/pages/schools.js',
                'resources/js/spa/pages/accounts.js',
                'resources/js/spa/pages/permissions.js',
                'resources/js/spa/pages/roles.js',
                'resources/js/auth/pages/register.js',
                'resources/js/auth/pages/login.js',
                'resources/js/spa/pages/customers.js',
                'resources/js/spa/pages/subscribers.js',
                'resources/js/spa/pages/lookups.js',
                'resources/js/spa/pages/plans.js',
                'resources/js/spa/pages/cards.js',
                'resources/js/spa/pages/group-cards.js',
                'resources/js/spa/pages/studio-albums.js',
                'resources/js/spa/pages/studio-customers.js',
                'resources/js/spa/pages/studio-profile.js',
                'resources/js/spa/pages/studio-storage.js',
                'resources/js/spa/pages/studio-photo-review.js',
                'resources/js/spa/pages/studio-cards.js',
                'resources/js/spa/pages/studio-card-detail.js',
                'resources/js/spa/pages/school-albums.js',
                'resources/js/spa/pages/school-cards.js',
                'resources/js/spa/pages/school-students.js',
                'resources/js/spa/pages/school-profile.js'
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
