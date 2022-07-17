const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: ['./storage/framework/views/*.php', './resources/views/**/*.blade.php', './resources/js/**/*.vue'],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
        color: {
            primary: "#087a9c"
        }
    },
    plugins: [require('@tailwindcss/typography')],
};
