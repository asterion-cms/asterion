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

optimize-images             Optimize the JPG, GIF and PNG images in the argv.site visual and stock folders
optimize-css                Minimise the CSS files in the argv.site/visual folder
optimize-html               Minimise the HTML files in the argv.site/cache folder
optimize                    Combine the optimize-css, optimize-html, optimize-images tasks

sass-compile-phonegap       Compile the SASS files in the argv.site/phonegap folder
sass-compile                Compile the SASS files in the argv.site/visual folder
sass                        Combine the sass-compile and sass-compile-phonegap

zip-all                     Zip the app and site files
ftp-zip                     Connect to an FTP server and send the site.zip and unzip.php files
unzip-server                Script to unzip a file in the server
package                     Package the whole site
package-ftp                 Package the site and send it to the server

*/

/**
* Variables
*/
'use strict';

var gulp = require('gulp');
var util = require('gulp-util');
var sass = require('gulp-sass');
var debug = require('gulp-debug-streams');
var shell = require('gulp-shell');
var minifyHTML = require('gulp-minify-html');
var autoprefixer = require('gulp-autoprefixer');
var uncss = require('gulp-uncss');
var minifyCss = require('gulp-minify-css');
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
    if (checkArguments()) {
        runSequence('optimize-css', 'optimize-html', 'optimize-images');
    }
});

/**
* SASS compilation
**/
gulp.task('sass-compile-phonegap', function(callback) {
    if (checkArguments()) {
        return gulp.src([argv.site+'/phonegap/www/visual/css/sass/*.scss'])
            .pipe(sass({outputStyle: 'compressed'})
                .on('error', sass.logError))
            .pipe(debug({title: 'Info:'}))
            .pipe(gulp.dest(argv.site + '/phonegap/www/visual/css/stylesheets/'));
    }
});

gulp.task('sass-compile', function(callback) {
    if (checkArguments()) {
        return gulp.src([argv.site+'/visual/css/sass/*.scss'])
            .pipe(sass({outputStyle: 'compressed'})
                .on('error', sass.logError))
            .pipe(debug({title: 'Info:'}))
            .pipe(gulp.dest(argv.site + '/visual/css/stylesheets/'));
    }
});

gulp.task('sass', function(callback) {
    if (checkArguments()) {
        gulp.watch([
                        argv.site + '/visual/**/*.scss',
                        argv.site + '/phonegap/www/visual/**/*.scss'
                    ], ['sass-compile', 'sass-compile-phonegap']);
    }
});


/**
* Production tasks
**/
gulp.task('copy-config-old', function(callback) {
    if (checkArguments()) {
        return gulp.src(argv.site + '/config/config.php')
                .pipe(rename(argv.site + '/config/config_old.php'))
                .pipe(gulp.dest('./'))
                .pipe(debug({title: 'Copying: ', minimal: true}));
    }
});

gulp.task('copy-config-prod', function(callback) {
    if (checkArguments()) {
        var typeConfig = (argv.typeConfig) ? argv.typeConfig : 'prod';
        return gulp.src(argv.site + '/config/config_' + typeConfig + '.php')
                .pipe(rename(argv.site + '/config/config.php'))
                .pipe(gulp.dest('./'))
                .pipe(debug({title: 'Copying: ', minimal: true}));
    }
});

gulp.task('copy-config-old-back', function(callback) {
    if (checkArguments()) {
        return gulp.src(argv.site + '/config/config_old.php')
                .pipe(rename(argv.site + '/config/config.php'))
                .pipe(gulp.dest('./'))
                .pipe(debug({title: 'Copying: ', minimal: true}));
    }
});

gulp.task('copy-unzip', function(callback) {
    if (checkArguments()) {
        return gulp.src('unzip.task')
                .pipe(rename('unzip.php'))
                .pipe(gulp.dest('./'))
                .pipe(debug({title: 'Copying: ', minimal: true}));
    }
});

gulp.task('zip-all', function(callback) {
    if (checkArguments()) {
        var siteFiles = gulp.src([argv.site + '/**/*', '!' + argv.site + '/**/config_*.php', '!' + argv.site + '/phonegap'], {base: "../fbapps"});
        var appFiles = gulp.src(['index.php', '.htaccess', 'robots.txt', 'app/**/*'], {base: "./"});
        return merge(siteFiles, appFiles)
                .pipe(gulpZip('complete.zip'))
                .pipe(gulp.dest('./'))
                .pipe(debug({title: 'Zipping app: ', minimal: true}));
    }
});

gulp.task('ftp-zip', function(callback) {
    if (checkArgumentsFtp()) {
        var ftpPort = (argv.port) ? argv.port : '21';
        var ftpRemotePath = (argv.remotePath) ? argv.remotePath : '/public_html';
        var conn = vinylFtp.create( {
            host:     argv.host,
            user:     argv.user,
            password: argv.pass,
            port:     ftpPort,
            parallel: 1
        });
        return gulp.src(['site.zip', 'unzip.php'])
            .pipe(conn.dest( ftpRemotePath))
            .pipe(debug({title: 'Sending: ', minimal: true}));
    }
});

gulp.task('unzip-server', function(callback) {
    if (checkArgumentsFtp()) {
        var remoteZipHost = (argv.remoteZipHost) ? argv.remoteZipHost : argv.host;
        return request('http://' + remoteZipHost + '/unzip.php')
            .pipe(debug({title: 'Server Unzip: ', minimal: true}));
    }
});

gulp.task('delete-files', function(callback) {
    if (checkArguments()) {
        del([
                'unzip.php', 'site.zip',
                argv.site + '/config/config_old.php'
            ]);
        return true;
    }
});

gulp.task('package', function(callback) {
    if (checkArgumentsFtp()) {
        runSequence('copy-config', 'copy-unzip', 'zip-all');
    }
});

gulp.task('package-ftp', function(callback) {
    if (checkArgumentsFtp()) {
        runSequence('copy-config-old', 'copy-config-prod', 'copy-unzip', 'zip-all', 'ftp-zip', 'unzip-server', 'delete-files', 'copy-config-old-back');
    }
});

/**
* General functions
*/
var checkArguments = function() {
    if (argv.site) {
        argv.site = (argv.site[argv.site.length - 1] == '/') ? argv.site.substr(0, (argv.site.length - 1)) : argv.site;
        return true;
    } else {
        console.log('You should specify a site to perform the task.');
        console.log('For ex.: gulp [task_name] --site asterion');
        return false;
    }
}

var checkArgumentsFtp = function() {
    if (argv.site && argv.host && argv.user && argv.pass) {
        return true;
    } else {
        console.log('You should specify a site, ftp-host, ftp-user and ftp-password to perform this task.');
        console.log('For ex.: gulp [task_name] --site asterion --host www.asterion.com --user asterion --pass password_asterion');
        return false;
    }
}