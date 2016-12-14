var gulp = require('gulp');
var util = require('gulp-util');
var debug = require('gulp-debug-streams');
var shell = require('gulp-shell');
var minifyHTML = require('gulp-minify-html');
var runSequence = require('run-sequence');
var autoprefixer = require('gulp-autoprefixer');
var uncss = require('gulp-uncss');
var minifyCss = require('gulp-minify-css');
var imagemin = require('gulp-imagemin');
var pngquant = require('imagemin-pngquant');
var jpegtran = require('imagemin-jpegtran');
var gifsicle = require('imagemin-gifsicle');
var replace = require('gulp-replace');
var fs = require('fs');
var download = require('gulp-download');
var argv = require('yargs').argv;

gulp.task('optimize-images', function () {
    if (argv.site) {
        return gulp.src([
                            argv.site+'/visual/**/*.jpg',
                            argv.site+'/visual/**/*.jpeg',
                            argv.site+'/visual/**/*.gif',
                            argv.site+'/visual/**/*.png',
                            argv.site+'/stock/**/*.jpg',
                            argv.site+'/stock/**/*.jpeg',
                            argv.site+'/stock/**/*.gif',
                            argv.site+'/stock/**/*.png'
                        ])
            .pipe(imagemin({
                progressive: false,
                svgoPlugins: [{removeViewBox: false}],
                use: [pngquant(), jpegtran(), gifsicle()]
            }))
            .pipe(debug({title: 'File:'}));
    } else {
        console.log('You should specify a site to optimize the images.');
        console.log('For ex.: gulp-images --site asterion');
    }
});

gulp.task('optimize-css', function() {
    if (argv.site) {
        return gulp.src('visual/css/**/*.css')
            .pipe(autoprefixer())
            .pipe(minifyCss({keepBreaks: false}))
            .pipe(gulp.dest('visual/css/'))
            .pipe(debug({title: 'File:'}));
    } else {
        console.log('You should specify a site to optimize the CSS.');
        console.log('For ex.: gulp optimize-css --site asterion');
    }
});

gulp.task('optimize-html', function() {
    if (argv.site) {
        return gulp.src(['cache/**/*.htm', 'cache/**/*.html'])
            .pipe(minifyHTML({
                quotes: true
            }))
            .pipe(gulp.dest('cache/'))
            .pipe(debug({title: 'File:'}));
    } else {
        console.log('You should specify a site to optimize the HTML.');
        console.log('For ex.: gulp optimize-html --site asterion');
    }
});

gulp.task('optimize', function(callback) {
    if (argv.site) {
        runSequence(
            'optimize-css',
            'optimize-html',
            'optimize-images',
            callback
        );
    } else {
        console.log('You should specify a site to optimize the files.');
        console.log('For ex.: gulp optimize --site asterion');
    }
});