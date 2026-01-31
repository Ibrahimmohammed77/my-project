import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                //
                // Core & Modules
                "resources/js/spa/core/index.js",
                "resources/js/spa/modules/accounts/index.js",
                "resources/js/spa/modules/roles/index.js",
                "resources/js/spa/modules/permissions/index.js",
                "resources/js/spa/modules/plans/index.js",
                "resources/js/spa/modules/customers/index.js",
                "resources/js/spa/modules/lookups/index.js",
                "resources/js/spa/modules/cards/index.js",
                "resources/js/spa/modules/schools/index.js",
                "resources/js/spa/modules/studios/index.js",
                "resources/js/spa/modules/subscribers/index.js",

                "resources/js/spa/modules/subscriptions/index.js",

                // Studio
                // Studio (Migrated)
                "resources/js/spa/contexts/studio/profile/index.js",
                "resources/js/spa/contexts/studio/albums/index.js",
                
                "resources/js/spa/contexts/studio/cards/index.js",
                "resources/js/spa/contexts/studio/customers/index.js",
                
                "resources/js/spa/contexts/studio/storage/index.js",
                
                "resources/js/spa/contexts/studio/photo-review/index.js",

                // School
                "resources/js/spa/contexts/school/profile/index.js",
                "resources/js/spa/contexts/school/albums/index.js",
                "resources/js/spa/contexts/school/cards/index.js",
                "resources/js/spa/contexts/school/students/index.js",

                // Auth
                "resources/js/auth/pages/register.js",
                "resources/js/auth/pages/login.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
