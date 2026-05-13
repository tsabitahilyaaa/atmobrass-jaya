/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                gold: {
                    DEFAULT: '#C8A951',
                    light: '#E8D48B',
                    dark: '#9A7B2C',
                    50: '#FDF8ED',
                },
                dark: {
                    DEFAULT: '#0F0F0F',
                    100: '#1A1A1A',
                    200: '#222222',
                    300: '#2A2A2A',
                    400: '#333333',
                },
                cream: '#F5F0E8',
                muted: '#9A9590',
            },
            fontFamily: {
                display: ['Playfair Display', 'serif'],
                body: ['DM Sans', 'sans-serif'],
            },
        },
    },
    plugins: [],
};