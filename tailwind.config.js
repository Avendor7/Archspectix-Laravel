/**
 * Tailwind CSS v4 configuration
 *
 * Note: Tailwind v4 no longer requires a config file by default. We only add
 * this file to customize theme tokens and scanning paths when needed.
 * See: https://tailwindcss.com/docs/configuration
 */

// Using the new "v4 preset style" config format. Keep this minimal and
// aligned with our resources/css/app.css @source declarations.

export default {
    // In v4, content can still be declared here for non-standard paths.
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,ts,vue}',
        './storage/framework/views/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.php',
    ],

    darkMode: "class", // Use class-based dark mode for consistent theming
    // No plugins needed currently; Vite plugin handles Tailwind processing.
    plugins: [],
};
