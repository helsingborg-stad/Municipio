// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass            = require('gulp-sass');
var concat          = require('gulp-concat');
var uglify          = require('gulp-uglify');
var cssnano         = require('gulp-cssnano');
var rename          = require('gulp-rename');
var autoprefixer    = require('gulp-autoprefixer');
var plumber         = require('gulp-plumber');
var rev             = require('gulp-rev');
var revDel          = require('rev-del');
var revReplaceCSS   = require('gulp-rev-css-url');

// Compile Our Sass
gulp.task('sass-dist', function() {
    return gulp.src([
                'assets/source/sass/app.scss',
                'assets/source/sass/admin.scss',
                'assets/source/sass/customizer.scss',
            ])
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.min'}))
            .pipe(cssnano({
                mergeLonghand: false,
                zindex: false
            }))
            .pipe(gulp.dest('assets/tmp/css'));
});

gulp.task('sass-dev', function() {
    return gulp.src([
                'assets/source/sass/app.scss',
                'assets/source/sass/admin.scss',
                'assets/source/sass/customizer.scss',
            ])
            .pipe(plumber())
            .pipe(sass())
            .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
            .pipe(rename({suffix: '.dev'}))
            .pipe(gulp.dest('assets/tmp/css'));
});

gulp.task("rev:sass", ['sass-dist', 'sass-dev'], function(){
  return revTask()
})

// Concatenate & Minify JS
gulp.task('scripts-dist', function() {
    gulp.src([
            'assets/source/js/*.js',
            'assets/source/js/**/*.js',
            '!assets/source/js/admin/*.js'
        ])
        .pipe(concat('packaged.js'))
        .pipe(gulp.dest('assets/dist/js'))
        .pipe(rename('packaged.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('assets/dist/js'));

    gulp.src('assets/source/mce-js/*.js')
            .pipe(uglify())
            .pipe(gulp.dest('assets/tmp/js'));
});

gulp.task('scripts-dist-admin', function() {
    return gulp.src([
                'assets/source/js/admin/*.js'
            ])
            .pipe(concat('admin.js'))
            .pipe(gulp.dest('assets/dist/js'))
            .pipe(rename('admin.min.js'))
            .pipe(uglify())
            .pipe(gulp.dest('assets/tmp/js'));
});

gulp.task("rev:scripts", ['scripts-dist', 'scripts-dist-admin'], function(){
  return revTask()
})

var revTask = function() {
    return gulp.src(["./assets/tmp/**/*"])
      .pipe(rev())
      .pipe(revReplaceCSS())
      .pipe(gulp.dest('assets/dist'))
      .pipe(rev.manifest())
      .pipe(revDel({dest: 'assets/dist'}))
      .pipe(gulp.dest('assets/dist'));
}

gulp.task("build", ['build:tmp'], function(){
  return revTask();
})

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch([
        'assets/source/js/**/*.js',
        'assets/source/mce-js/**/*.js'
        ], ['rev:scripts']);
    gulp.watch('assets/source/sass/**/*.scss', ['rev:sass']);
    //gulp.watch('assets/source/images/**/*', ['imagemin']);
});

gulp.task('build:tmp', ['sass-dist', 'sass-dev', 'scripts-dist', 'scripts-dist-admin']);

// Default Task
gulp.task('default', ['build', 'watch']);
