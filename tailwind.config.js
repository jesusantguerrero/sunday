const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: {
        mode: 'layers',
        options: {
            whitelist: [
                'bg-red-400',
                'bg-green-400',
                'bg-yellow-400',
                'bg-blue-400',
                'bg-purple-400',
                'font-bold',
                'bg-red-300',
                'bg-green-300',
                'bg-yellow-300',
                'bg-blue-300',
                'bg-purple-300',
            ]
        },
        content: ['./storage/framework/views/*.php', './resources/views/**/*.blade.php', './resources/js/**/*.vue']
    },
    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    variants: {
        opacity: ['responsive', 'hover', 'focus', 'disabled'],
    },

    plugins: [require('@tailwindcss/ui'), require('@tailwindcss/typography')],
};
