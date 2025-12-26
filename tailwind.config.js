/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./index.php",
        "./admin/**/*.php",
        "./*.php",
        "./templates/**/*.php",
        "./assets/js/**/*.js"
    ],
    theme: {
        extend: {
            colors: {
                // Dark theme backgrounds
                dark: {
                    900: '#0f0f0f',
                    800: '#141414',
                    700: '#1a1a1a',
                    600: '#1e1e1e',
                    500: '#2a2a2a',
                    400: '#3a3a3a',
                },
                // Primary accent (Netflix-style red)
                primary: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#e50914',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                // Secondary (blue for interactive)
                secondary: {
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                }
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
            },
        },
    },
    plugins: [],
}