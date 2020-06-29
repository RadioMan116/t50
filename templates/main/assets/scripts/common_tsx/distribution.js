const path = require('path');
const fs = require('fs');
const childProcess = require('child_process');

const pathComponents = path.resolve(__dirname, "../".repeat(5), "components/t50")
if (!fs.existsSync(pathComponents))
    throw new Error("components path not exists!")

const search = (dir, filter, callback, main = false) => {
    var results = [];
    fs.readdir(dir, function (err, list) {
        if (err) throw err;
        let countFiles = list.length
        if (!countFiles) callback(results)

        list.forEach(function (file) {
            file = path.resolve(dir, file);
            fs.stat(file, function (err, stat) {
                if (err) throw err;

                if (stat.isDirectory()) {
                    search(file, filter, function (res) {
                        results = results.concat(res);
                        if (!--countFiles) callback(results)
                    });
                } else {
                    baseName = path.basename(file)
                    if (filter.indexOf(baseName) != -1)
                        results.push(file);

                    if (!--countFiles) callback(results)
                }
            });
        });
    });
};



const getSrcFiles = () => {
    dir = path.resolve(__dirname, "src");
    return new Promise(resolve => {
        fs.readdir(dir, function (err, list) {
            map = {}
            list.forEach(baseName => {
                map[baseName] = path.resolve(dir, baseName);
            })
            resolve(map)
        })
    })
}

const compile = (distFiles) => {
    let dir, dirs = [];
    distFiles.forEach(file => {
        dir = path.dirname(file)
        if (dirs.indexOf(dir) == -1)
            dirs.push(dir)
    })

    let tmp, tsDir, tsDirs = [];
    dirs.forEach(dir => {
        tsDir = dir;
        while (true) {
            tmp = path.resolve(tsDir, "..");
            if (tmp == tsDir) {
                tsDir = null
                break
            }

            if (fs.existsSync(path.resolve(tsDir, "webpack.config.js")))
                break

            tsDir = tmp
        }
        if (tsDir != null)
            tsDirs.push(tsDir)
    })

    tsDirs.forEach(tsDir => {
        childProcess.exec("yarn public", {cwd: tsDir}, (error, stdout, stderr) => {
            console.log(`\n\n-----------\n${tsDir}\n-----------`);
            if (error) {
                console.error(`exec error: ${error}`);
                return;
            }
            console.log(`stdout: ${stdout}`);
            console.error(`stderr: ${stderr}`);
        });
    })
}

const Main = async () => {
    let sources = await getSrcFiles();

    search(pathComponents, Object.keys(sources), function (distFiles) {
        distFiles.forEach(distFile => {
            distFileName = path.basename(distFile);
            sourceFile = sources[distFileName];
            fs.copyFile(sourceFile, distFile, (err) => {
                if (err) throw err;
            });
        })
        compile(distFiles)
    }, true);
}

Main();

