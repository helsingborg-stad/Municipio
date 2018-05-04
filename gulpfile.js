//
//  GULPFILE.JS
//  Author: Nikolas Ramstedt (nikolas.ramstedt@helsingborg.se)
//
//  CHEATSHEET:
//  "gulp"                  -   Build and watch combined
//  "gulp watch"            -   Watch for file changes and compile changed files
//  "gulp build"            -   Re-build dist folder and build assets
//
//
// => ATTENTION: use "npm install" before first build!

/* ==========================================================================
   Dependencies
   ========================================================================== */

    var gulp        =   require('gulp'),
    rename          =   require('gulp-rename'),
    sass            =   require('gulp-sass'),
    concat          =   require('gulp-concat'),
    autoprefixer    =   require('gulp-autoprefixer'),
    sourcemaps      =   require('gulp-sourcemaps'),
    uglify          =   require('gulp-uglify'),
    rev             =   require('gulp-rev'),
    revDel          =   require('rev-del'),
    revReplaceCSS   =   require('gulp-rev-css-url'),
    del             =   require('del'),
    runSequence     =   require('run-sequence'),
    plumber         =   require('gulp-plumber'),
    jshint          =   require("gulp-jshint"),
    cleanCSS        =   require('gulp-clean-css'),
    node_modules    =   'node_modules/';

/* ==========================================================================
   Load configuration file
   ========================================================================== */

    var config = (require('fs').existsSync('./config.json') ? JSON.parse(require('fs').readFileSync('./config.json')) : {});

/* ==========================================================================
   Default task
   ========================================================================== */

    gulp.task('default', function(callback) {
        runSequence('build', 'watch', callback);
    });

/* ==========================================================================
   Build tasks
   ========================================================================== */

    gulp.task('build', function(callback) {
        runSequence('clean:dist', ['sass', 'scripts'], 'rev', callback);
    });

    gulp.task('build:sass', function(callback) {
        runSequence('sass', 'rev', callback);
    });

    gulp.task('build:scripts', function(callback) {
        runSequence('scripts', 'rev', callback);
    });

/* ==========================================================================
   Watch task
   ========================================================================== */

    gulp.task('watch', function() {
        gulp.watch('./assets/source/sass/**/*.scss', ['build:sass']);
        gulp.watch('./assets/source/js/**/*.js', ['build:scripts']);
    });

/* ==========================================================================
   Rev task
   ========================================================================== */

    gulp.task("rev", function(){
        return gulp.src(["./assets/tmp/**/*"])
          .pipe(rev())
          .pipe(revReplaceCSS())
          .pipe(gulp.dest('./assets/dist'))
          .pipe(rev.manifest())
          .pipe(revDel({dest: './assets/dist'}))
          .pipe(gulp.dest('./assets/dist'));
    });

/* ==========================================================================
   SASS Task
   ========================================================================== */

    gulp.task('sass', function () {
        var app = gulp.src('assets/source/sass/app.scss')
                .pipe(plumber())
                .pipe(sourcemaps.init())
                .pipe(sass().on('error', sass.logError))
                .pipe(autoprefixer({
                        browsers: ['last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1']
                    }))
                .pipe(sourcemaps.write())
                .pipe(gulp.dest('./assets/dist/css'))
                .pipe(cleanCSS({debug: true}))
                .pipe(gulp.dest('./assets/tmp/css'));

        var admin = gulp.src('assets/source/sass/admin.scss')
                .pipe(plumber())
                .pipe(sourcemaps.init())
                .pipe(sass().on('error', sass.logError))
                .pipe(autoprefixer({
                        browsers: ['last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1']
                    }))
                .pipe(sourcemaps.write())
                .pipe(gulp.dest('./assets/dist/css'))
                .pipe(cleanCSS({debug: true}))
                .pipe(gulp.dest('./assets/tmp/css'));

        return [app, admin];
    });

/* ==========================================================================
   Scripts task
   ========================================================================== */

    gulp.task('scripts', function() {
        var app = gulp.src(['assets/source/js/app.js', 'assets/source/js/*/*.js', '!assets/source/js/admin/*.js'])
            .pipe(plumber())
            .pipe(sourcemaps.init())
            .pipe(jshint({multistr: true}))
            .pipe(jshint.reporter("default"))
            .pipe(concat('app.js'))
            .pipe(sourcemaps.write())
            .pipe(gulp.dest('./assets/dist/js'))
            .pipe(uglify())
            .pipe(gulp.dest('./assets/tmp/js'));

        var admin = gulp.src(['assets/source/js/admin/*.js'])
            .pipe(plumber())
            .pipe(sourcemaps.init())
            .pipe(jshint({multistr: true}))
            .pipe(jshint.reporter("default"))
            .pipe(concat('admin.js'))
            .pipe(sourcemaps.write())
            .pipe(gulp.dest('./assets/dist/js'))
            .pipe(uglify())
            .pipe(gulp.dest('./assets/tmp/js'));

        var vendor = gulp.src([
                'assets/source/js/vendor/*.js'
            ])
            .pipe(plumber())
            .pipe(sourcemaps.init())
            .pipe(concat('vendor.js'))
            .pipe(sourcemaps.write())
            .pipe(gulp.dest('./assets/dist/js'))
            .pipe(uglify())
            .pipe(gulp.dest('./assets/tmp/js'));

        var mce = gulp.src([
                'assets/source/mce-js/*.js'
            ])
            .pipe(plumber())
            .pipe(sourcemaps.init())
            .pipe(concat('mce.js'))
            .pipe(sourcemaps.write())
            .pipe(gulp.dest('./assets/dist/js'))
            .pipe(uglify())
            .pipe(gulp.dest('./assets/tmp/js'));

        return [app, vendor, admin];

    });

/* ==========================================================================
   Clean/Clear tasks
   ========================================================================== */

    gulp.task('clean:dist', function () {
        return del.sync('./assets/dist');
    });

    gulp.task('clean:tmp', function () {
        return del.sync('./assets/tmp');
    });
