
const gulp = require('gulp');
const sass = require('gulp-sass');
const csso = require('gulp-csso');
const terser = require('gulp-terser');
const rollup = require('gulp-rollup');
const sourcemaps = require('gulp-sourcemaps');
const hash = require('gulp-hash');
const iconfont = require('gulp-iconfont');
const iconfontCss = require('gulp-iconfont-css');
const fs = require('fs');

gulp.task('sass', function() {
    return gulp.src('resources/scss/**/*.scss')
        .pipe(sass({
            outputStyle: 'nested'
        }))
        // .pipe(csso())
        .pipe(gulp.dest('public/css'));
});

gulp.task('js', function() {
    return gulp.src(['resources/js/**/*.js', '!resources/js/{utils,components}/*.js'])
        .pipe(sourcemaps.init())
        .pipe(terser())
        .pipe(sourcemaps.write('./maps/'))
        .pipe(gulp.dest('public/js'));
});

gulp.task('rollup-utils', function() {
    return gulp.src('resources/js/**/*.js')
        .pipe(sourcemaps.init())
        .pipe(rollup({
            input: 'resources/js/base/utils.js',
            output: {
                format: 'umd',
                name: 'utils'
            }
        }))
        .pipe(terser())
        .pipe(sourcemaps.write('./maps/'))
        .pipe(gulp.dest('public/js'));
});

gulp.task('rollup', function() {
    return gulp.src('resources/js/**/*.js')
        .pipe(sourcemaps.init())
        .pipe(rollup({
            input: 'resources/js/app.js',
            external: ['utils', 'anime'],
            output: {
                format: 'umd',
                name: 'app',
                globals: {
                    utils: 'utils',
                    anime: 'anime'
                }
            }
        }))
        .pipe(terser())
        .pipe(sourcemaps.write('./maps/'))
        .pipe(gulp.dest('public/js'));
});

gulp.task('dev', function() {
    gulp.series('sass', 'rollup')();
    gulp.watch('resources/js/**/*.js', gulp.series('rollup'));
    gulp.watch('resources/scss/**/*.scss', gulp.series('sass'));
});

const fontName = 'icons';
const fontFile = fontName + '.css';

gulp.task('icons', function() {
    gulp.src('resources/icons/*.svg')
        .pipe(iconfontCss({
            fontName: fontName,
            targetPath: fontFile,
            cssClass: 'ic',
            path: 'resources/icons/template.css'
        }))
        .pipe(iconfont({
            fontName: fontName,
            normalize: true,
            fontHeight: 1001
        }))
        .pipe(gulp.dest('public/icons/'));
});

gulp.task('icons-minify', function() {
    gulp.src('public/icons/*.css')
        .pipe(csso())
        .pipe(gulp.dest('public/icons/'));
});
