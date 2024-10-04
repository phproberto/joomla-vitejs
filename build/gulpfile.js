const { dest, parallel, series, src } = require('gulp');
const zip = require('gulp-zip');
const debug = require('gulp-debug');
const fs = require('fs');
const xml2js = require('xml2js');
const parser = new xml2js.Parser();

const config = require('./gulp-config.js');
const browserSync = require('./lib/browserSync.js');

// Plugins: System
const vitejsJoomlaSystemPlugin = require('./gulp-extensions/plugins/system/vitejs_joomla.js');

const clean = parallel(
    vitejsJoomlaSystemPlugin.clean,
);

const copy = parallel(
    vitejsJoomlaSystemPlugin.copy,
);

const cleanCopy = series(clean, copy);

const importFromSite = parallel(
    vitejsJoomlaSystemPlugin.import,
);

function release(cb) {
    fs.readFile(config.folder.extensions + '/plugins/system/vitejs_joomla/vitejs_joomla.xml', function (err, data) {
        parser.parseString(data, function (err, result) {
            var version = result.extension.version[0];

            var fileName = result.extension.name[0] + '-v' + version + '.zip';

            const files = [
                config.folder.extensions + '/plugins/system/vitejs_joomla/**',
            ];

            return src(files)
                .pipe(debug({ title: 'ZIP file content:' }))
                .pipe(zip(fileName))
                .pipe(dest('releases'))
                .on('end', cb);
        });
    });
}

const watch = parallel(
    vitejsJoomlaSystemPlugin.watch,
);

const dev = series(
    cleanCopy,
    parallel(browserSync.init, watch)
);

module.exports = {
    clean,
    copy,
    cleanCopy,
    default: dev,
    dev,
    import: importFromSite,
    release: series(release),
    watch,
}