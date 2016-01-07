// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var minifycss = require('gulp-minify-css');
var rename = require('gulp-rename');
var autoprefixer = require('gulp-autoprefixer');
var plumber = require('gulp-plumber');

// Compile Our Sass
gulp.task('sass-dist', function() {
    return gulp.src('assets/source/sass/app.scss')
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.min'}))
            .pipe(minifycss())
            .pipe(gulp.dest('assets/dist/css'))
});

gulp.task('sass-dev', function() {
    return gulp.src('assets/source/sass/app.scss')
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.dev'}))
            .pipe(gulp.dest('assets/dist/css'))
});

// Concatenate & Minify JS
gulp.task('scripts-dist', function() {
    return gulp.src('assets/source/js/*.js')
            .pipe(concat('packaged.js'))
            .pipe(gulp.dest('assets/dist/js'))
            .pipe(rename('packaged.min.js'))
            .pipe(uglify())
            .pipe(gulp.dest('assets/dist/js'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('assets/source/js/**/*.js', ['scripts-dist']);
    gulp.watch('assets/source/sass/**/*.scss', ['sass-dist', 'sass-dev']);
});

// Default Task
gulp.task('default', ['sass-dist', 'sass-dev', 'scripts-dist', 'watch']);

