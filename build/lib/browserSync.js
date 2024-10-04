
var config = require('../gulp-config');
var browserSync = require('browser-sync').create();

function initBrowserSync() {
    return browserSync.init(config.browserConfig);
}

function reloadBrowserSync(cb) {
    browserSync.reload();
    cb();
}

module.exports = {
    instance: browserSync,
    init: initBrowserSync,
    reload: reloadBrowserSync,
}