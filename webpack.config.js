const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            '@components': path.resolve('resources/js/components'),
        },
    },
};
