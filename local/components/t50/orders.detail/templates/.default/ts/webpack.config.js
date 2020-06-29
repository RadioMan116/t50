const path = require('path');
module.exports = {
    entry: __dirname + '/src/Main.tsx',
    output: {
        path: path.resolve(__dirname, ".."),
        filename: 'script.js',
    },
    resolve: {
        extensions: ['.ts', '.tsx'],
        alias: {
            "@Root": path.resolve(__dirname, `src/`),
            "@Components": path.resolve(__dirname, `src/components/`),
            "@Conteiners": path.resolve(__dirname, `src/conteiners/`),
            "@Reducers": path.resolve(__dirname, `src/reducers/`),
            "@Actions": path.resolve(__dirname, `src/actions/`),
        }
    },
    devtool: 'inline-source-map',
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
        "preact": "preact",
    }
};