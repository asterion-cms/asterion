/**
* @file
*
* The gulpfile.js has all the main tasks to compile and publish a website.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/

/**
*
* MAIN TASKS
*
* optimize-images             Optimize the JPG, GIF and PNG images in the argv.site visual and stock folders
* optimize-css                Minimize the CSS files in the argv.site/visual folder
* optimize-html               Minimize the HTML files in the argv.site/cache folder
* optimize                    Combine the optimize-css, optimize-html, optimize-images tasks
*
* sass                        Watch and compile the sass files in the /base and /app directories
* scripts                     Watch and merge the JS files in the /base and /app directories
*
* build                       Run the sass, scripts and optimize tasks
*
**/

/**
* Variables
*/
'use strict';

var gulp = require('gulp');
var util = require('gulp-util');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var debug = require('gulp-debug-streams');
var shell = require('gulp-shell');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var minify = require('gulp-minify');
var minifyHTML = require('gulp-minify-html');
var minifyCss = require('gulp-minify-css');
var autoprefixer = require('gulp-autoprefixer');
var uncss = require('gulp-uncss');
var imagemin = require('gulp-imagemin');
var pngquant = require('imagemin-pngquant');
var jpegtran = require('imagemin-jpegtran');
var gifsicle = require('imagemin-gifsicle');
var imageminMozjpeg = require('imagemin-mozjpeg');
var gulpCopy = require('gulp-copy');
var rename = require('gulp-rename');
var gulpZip = require('gulp-zip');
var vinylFtp = require('vinyl-ftp');
var fs = require('fs');
var del = require('del');
var runSequence = require('run-sequence');
var argv = require('yargs').argv;
var request = require('request');
var merge = require('merge-stream');

/**
* Optimization tasks
**/
gulp.task('optimize-images', function () {
    if (checkArguments()) {
        return gulp.src([
                            argv.site + '/visual/**/*.jpg',
                            argv.site + '/visual/**/*.jpeg',
                            argv.site + '/visual/**/*.gif',
                            argv.site + '/visual/**/*.png',
                            argv.site + '/stock/**/*.jpg',
                            argv.site + '/stock/**/*.jpeg',
                            argv.site + '/stock/**/*.gif',
                            argv.site + '/stock/**/*.png'
                        ], {base: "./"})
            .pipe(imagemin({
                progressive: false,
                svgoPlugins: [{removeViewBox: false}],
                use: [pngquant(), jpegtran(), gifsicle(), imageminMozjpeg()]
            }))
            .pipe(debug({title: 'Info:'}))
            .pipe(gulp.dest("./"));
    }
});

gulp.task('optimize-css', function() {
    if (checkArguments()) {
        return gulp.src([
                            argv.site + '/visual/css/**/*.css'
                        ], {base: "./"})
            .pipe(autoprefixer())
            .pipe(minifyCss({keepBreaks: false}))
            .pipe(debug({title: 'Info:'}))
            .pipe(gulp.dest("./"));
    }
});

gulp.task('optimize-html', function() {
    if (checkArguments()) {
        return gulp.src([
                            argv.site + '/cache/**/*.htm',
                            argv.site + '/cache/**/*.html'
                        ], {base: "./"})
            .pipe(minifyHTML({
                quotes: true
            }))
            .pipe(debug({title: 'Info:'}))
            .pipe(gulp.dest("./"));
    }
});

gulp.task('optimize', function(callback) {
    if (checkArguments()) runSequence('optimize-css', 'optimize-html', 'optimize-images');
});

/**
* SCRIPTS compilation
**/
gulp.task('scripts-compile-app', function() { if (checkArguments()) return gulpScripts('app'); });
gulp.task('scripts-compile-base', function() { if (checkArguments()) return gulpScripts('base'); });
gulp.task('scripts-compile', function() { if (checkArguments()) runSequence('scripts-compile-app', 'scripts-compile-base'); });

gulp.task('scripts', function() {
    if (checkArguments()) gulp.watch([argv.site + '/**/libjs/src/*.js'], ['scripts-compile-app', 'scripts-compile-base']);
});

/**
* SASS compilation
**/
gulp.task('sass-compile-base', function() { if (checkArguments()) return gulpSass('base'); });
gulp.task('sass-compile-app', function() { if (checkArguments()) return gulpSass('app'); });
gulp.task('sass-compile', function() { if (checkArguments()) runSequence('sass-compile-app', 'sass-compile-base'); });

gulp.task('sass', function() {
    if (checkArguments()) gulp.watch([argv.site + '/**/visual/css/sass/*.scss'], ['sass-compile-base', 'sass-compile-app']);
});

/**
* WATCH the whole project
**/
gulp.task('watch', function() {
    if (checkArguments()) gulp.watch([argv.site + '/**/visual/css/sass/*.scss', argv.site + '/**/libjs/src/*.js'], ['sass-compile-base', 'sass-compile-app', 'scripts-compile-app', 'scripts-compile-base']);
});

/**
* BUILD the whole project
**/
gulp.task('build', function() { if (checkArguments()) runSequence('sass-compile', 'scripts-compile', 'optimize'); });

/**
* General functions
*/
var checkArguments = function() {
    argv.site = (argv.site) ? ((argv.site[argv.site.length - 1] == '/') ? argv.site.substr(0, (argv.site.length - 1)) : argv.site) : '.';
    return true;
}

var gulpSass = function(directory) {
    let inputFiles = [argv.site + '/' + directory + '/visual/css/sass/*.scss'];
    return gulp.src(inputFiles)
                .pipe(sass({outputStyle: 'compressed'})
                    .on('error', sass.logError))
                .pipe(debug({title: 'Info:'}))
                .pipe(gulp.dest(argv.site + '/' + directory + '/visual/css/stylesheets/'));
}

var gulpScripts = function(directory) {
    let modulesJson = require(argv.site + '/' + directory + '/libjs/src/modules.json');
    let inputFiles = modulesJson.map((item) => argv.site + item);
    inputFiles.push(argv.site + '/' + directory + '/libjs/src/*.js');
    return gulp.src(inputFiles)
                .pipe(concat('dist.js'))
                .pipe(minify())
                .pipe(debug({title: 'Info:'}))
                .pipe(gulp.dest(argv.site + '/' + directory + '/libjs/'));
}
