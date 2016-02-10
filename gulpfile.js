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
    return gulp.src('assets/source/sass/app.scss')
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.min'}))
            .pipe(cssnano())
            .pipe(gulp.dest('assets/dist/css'));
});

gulp.task('sass-dev', function() {
    return gulp.src('assets/source/sass/app.scss')
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.dev'}))
            .pipe(gulp.dest('assets/dist/css'));
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

// Compress images
//gulp.task('imagemin', function () {
//    return gulp.src('assets/source/images/**/*')
//        .pipe(imagemin({
//            progressive: true,
//            svgoPlugins: [{removeViewBox: false}],
//            use: [pngquant()]
//        }))
//        .pipe(gulp.dest('assets/dist/images'));
//});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('assets/source/js/**/*.js', ['scripts-dist']);
    gulp.watch('assets/source/sass/**/*.scss', ['sass-dist', 'sass-dev']);
    //gulp.watch('assets/source/images/**/*', ['imagemin']);
});

// Default Task
gulp.task('default', ['sass-dist', 'sass-dev', 'scripts-dist', 'watch']);

