import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/admin.css",
                "resources/css/auth.css",
                "resources/css/dashboard.css",
                "resources/css/mainpage.css",
                "resources/css/profile.css",
                "resources/css/sidebar.css",
                "resources/js/bootstrap.js",
                "resources/js/dashboard.js",
                "resources/js/mainpage.js",
                "resources/js/profile.js",
                "resources/js/sidebar.js",
            ],
            refresh: true,
        }),
    ],
});
