const path = require('path');
module.exports = {
    entry: __dirname + '/src/index.tsx',
    output: {
        path: path.resolve(__dirname, ".."),
        filename: 'script.js',
    },
    resolve: {
        extensions: ['.ts', '.tsx'],
    },
    mode: "development",
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                exclude: /node_modules/,
                loaders: ['ts-loader']
            }
        ]
    },
    externals: {
        "preact": "preact"
    }
};