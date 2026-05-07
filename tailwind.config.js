const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: '#dc2626',
                secondary: '#3b82f6',
            },
            screens: {
                'xs': '475px',
            }
        },
    },
    plugins: [],
}; 