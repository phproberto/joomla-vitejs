const { dest, series, parallel, src, watch } = require('gulp');
var del = require('del');
const browserSync = require('../../../lib/browserSync.js');

var config = require('../../../gulp-config.js');

const pluginType       = 'system';
const pluginId         = 'vitejs_joomla';
const extensionFolder  = `${config.folder.extensions}/plugins/${pluginType}/${pluginId}`;
const mediaPath = `${extensionFolder}/media/${pluginId}`;
const wwwExtensionPath = `${config.folder.www}/plugins/${pluginType}/${pluginId}`;
const wwwMediaPath = `${config.folder.www}/media/${pluginId}`;

function cleanVitejsJoomlaSystemPlugin() {
    return del(wwwExtensionPath, { force: true });
}

function cleanVitejsJoomlaSystemPluginMedia() {
    return del(wwwMediaPath, { force: true });
}

function copyVitejsJoomlaSystemPlugin() {
    return src(extensionFolder + '/**')
        .pipe(dest(wwwExtensionPath));
}

function copyVitejsJoomlaSystemPluginMedia() {
    return src(mediaPath + '/**')
        .pipe(dest(wwwMediaPath));
}

function importVitejsJoomlaSystemPluginExtension() {
    return src(wwwExtensionPath + '/**')
        .pipe(dest(extensionFolder));
}

function watchVitejsJoomlaSystemPluginForChanges() {
    watch([
        extensionFolder + '/**',
        `!${mediaPath}/**`,
    ],
        series(cleanVitejsJoomlaSystemPlugin, copyVitejsJoomlaSystemPlugin, browserSync.reload)
    );
}

function watchVitejsJoomlaSystemPluginMedia() {
    watch(mediaPath + '/**', series(cleanVitejsJoomlaSystemPluginMedia, copyVitejsJoomlaSystemPluginMedia, browserSync.reload));
}

const watchVitejsJoomlaSystemPlugin = parallel(
    watchVitejsJoomlaSystemPluginForChanges, watchVitejsJoomlaSystemPluginMedia
);

const importVitejsJoomlaSystemPluginFromSite = parallel(importVitejsJoomlaSystemPluginExtension);
const copy = parallel(copyVitejsJoomlaSystemPlugin, copyVitejsJoomlaSystemPluginMedia);

module.exports = {
    clean: cleanVitejsJoomlaSystemPlugin,
    copy,
    cleanCopy: series(cleanVitejsJoomlaSystemPlugin, copyVitejsJoomlaSystemPlugin),
    import: importVitejsJoomlaSystemPluginFromSite,
    watch: watchVitejsJoomlaSystemPlugin,
};
