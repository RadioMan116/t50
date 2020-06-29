const path = require('path');
const fs = require('fs');
const argv = require('yargs').argv

class PrepareDescriptionPlugin {
    apply(compiler) {
        if( argv.w )
            return

        compiler.hooks.done.tap('PrepareDescription', (stats) => {
            this.exec()
        });
    }

    async exec() {
        await fs.readdir(__dirname + "/types/", async (err, list) => {
            if (err) throw err;
            list = list.filter(name => {
                return ["package.json", "index.d.ts"].indexOf(name) == -1
            })
            let code = ""
            for (let file of list) {
                let path = __dirname + "/types/" + file
                code += await this.readCode(path)
            }
            this.writeCode(this.prepareCode(code))
        })
    }

    prepareCode(code) {
        let result = code + ""
        result = result.replace(/export {};/g, "")
        result = result.replace(/export default/g, "declare")
        result = result.replace(/export declare/g, "declare")
        result = result.replace(/export /g, "declare ")
        return result
    }

    writeCode(code) {
        fs.writeFile(__dirname + "/types/index.d.ts", code, (err) => {
            if (err) throw err;
            console.log("UPDATE DESCRIPTION DONE");
            this.copyToNodeModules();
        });
    }

    copyToNodeModules() {
        let dir = './node_modules/@types/t50_common_details/'
        // if (!fs.existsSync(dir))
        //     fs.mkdirSync(dir);

        fs.copyFile(__dirname + '/types/index.d.ts', dir + "index.d.ts", err => {
            if (err) throw err;
            console.log("copy to t50_common_details");
        });
    }

    readCode(path) {
        return new Promise(resolve => {
            fs.readFile(path, function (err, data) {
                if (err) throw err;
                resolve(data.toString())
            });
        })
    }
}

module.exports = {
    entry: __dirname + '/src/index.ts',
    output: {
        path: path.resolve(__dirname, ".."),
        filename: 'common_details.js',
        libraryExport: 'default',
        libraryTarget: 'window'
    },
    resolve: {
        extensions: ['.ts'],
    },
    mode: ( argv.w ? "development" : "production" ),
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                exclude: /node_modules/,
                loaders: ['ts-loader']
            }
        ]
    },
    plugins: [
        new PrepareDescriptionPlugin()
    ]
};

// if( argv.w != true )
//     module.exports.plugins.push(new PrepareDescriptionPlugin())
