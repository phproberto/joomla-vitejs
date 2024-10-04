const { dest, src } = require('gulp');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const minify = require('gulp-minify');
const sourcemaps = require('gulp-sourcemaps');

function compile(source, destFolder, destFile) {
    return src(source)
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: [
                ['@babel/preset-env', { modules: false, forceAllTransforms: true }]
            ],
            plugins: [
                '@babel/plugin-syntax-dynamic-import',
                '@babel/plugin-proposal-object-rest-spread',
                [
                    '@babel/plugin-transform-runtime',
                    {
                        helpers: false
                    }
                ]
            ]
        }))
        .pipe(concat(destFile))
        .pipe(sourcemaps.write('.'))
        .pipe(minify({
            ext: {
                src: '.js',
                min: '.min.js'
            }
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(dest(destFolder));
}

module.exports = {
    compile,
}
