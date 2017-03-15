// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var cssnano = require('gulp-cssnano');
var rename = require('gulp-rename');
var autoprefixer = require('gulp-autoprefixer');
var plumber = require('gulp-plumber');
//var imagemin = require('gulp-imagemin');
//var pngquant = require('imagemin-pngquant'); // $ npm i -D imagemin-pngquant

// Compile Our Sass
gulp.task('sass-dist', function() {
    return gulp.src([
                'assets/source/sass/app.scss',
                'assets/source/sass/admin.scss'
            ])
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.min'}))
            .pipe(cssnano({
                mergeLonghand: false,
                zindex: false
            }))
            .pipe(gulp.dest('assets/dist/css'));
});

gulp.task('sass-dev', function() {
    return gulp.src([
                'assets/source/sass/app.scss',
                'assets/source/sass/admin.scss'
            ])
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.dev'}))
            .pipe(gulp.dest('assets/dist/css'));
});

// Concatenate & Minify JS
gulp.task('scripts-dist', function() {
    gulp.src([
            'assets/source/js/**/*.js',
            '!assets/source/js/admin/*.js',
            '!assets/source/js/font.js'
        ])
        .pipe(concat('packaged.js'))
        .pipe(gulp.dest('assets/dist/js'))
        .pipe(rename('packaged.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('assets/dist/js'));

    gulp.src('assets/source/js/font.js')
            .pipe(rename('font.min.js'))
            .pipe(uglify())
            .pipe(gulp.dest('assets/dist/js'));

    gulp.src('assets/source/mce-js/*.js')
            .pipe(uglify())
            .pipe(gulp.dest('assets/dist/js'));
});

gulp.task('scripts-dist-admin', function() {
    return gulp.src([
                'assets/source/js/admin/*.js'
            ])
            .pipe(concat('admin.js'))
            .pipe(gulp.dest('assets/dist/js'))
            .pipe(rename('admin.min.js'))
            .pipe(uglify())
            .pipe(gulp.dest('assets/dist/js'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch([
        'assets/source/js/**/*.js',
        'assets/source/mce-js/**/*.js'
        ], ['scripts-dist', 'scripts-dist-admin']);
    gulp.watch('assets/source/sass/**/*.scss', ['sass-dist', 'sass-dev']);
    //gulp.watch('assets/source/images/**/*', ['imagemin']);
});

// Default Task
gulp.task('default', ['sass-dist', 'sass-dev', 'scripts-dist', 'scripts-dist-admin', 'watch']);

