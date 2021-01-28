const path = require('path');

module.exports = {
    module: {
        rules: [
            {
                test: /\.md$/,
                use: ["html-loader", "markdown-loader"],
            }
        ]
    },
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            '@components': path.resolve('resources/js/components'),
            '@docs': path.resolve('resources/js/documentation'),
            '@root': '/',
            ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
        },
    },
};
