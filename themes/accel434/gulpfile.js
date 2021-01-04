var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var gulp = require('gulp');
var rename = require('gulp-rename');
var cheerio = require('gulp-cheerio');
var notify = require('gulp-notify');

// Sass Plugins
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');

// SVG Plugins
var svgmin = require('gulp-svgmin');
var svgstore = require('gulp-svgstore');

// Minifiy JS
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var processors = [
    autoprefixer
]

gulp.task('sass', function () {


    gulp.src('assets/styles/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
        includePaths: require('node-neat').includePaths
    }).on('error', sass.logError))
    .pipe(postcss( processors ))
    .pipe(sourcemaps.write('./assets/maps'))
    .pipe(gulp.dest('./'))
    .pipe(notify({message: 'Regular styles finished.'}));
});

gulp.task('editor-sass', function () {
    gulp.src('assets/styles/editor-style.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
        includePaths: require('node-neat').includePaths
    }).on('error', sass.logError))
    .pipe(postcss( processors ))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./'))
    .pipe(notify({message: 'Editor styles finished.'}));
});

gulp.task('svgstore', function() {
    return gulp
        .src('assets/icons/src/*.svg')
        .pipe(rename({prefix: 'ico-'}))
        .pipe(svgmin())
        .pipe(svgstore())
        .pipe(cheerio({
            run: function($) {
                $("[fill]").removeAttr("fill");
            },
            parserOptions: { xmlMode: true }
        }))
        .pipe(gulp.dest("assets/icons/dist/"))
        .pipe(notify({message: 'SVG proccess finished.'}));
});

//script paths
var jsFiles = 'assets/js/*.js',
    jsDest = 'assets/js/build/';

gulp.task('scripts', function() {
    return gulp.src(jsFiles)
        .pipe(concat('site-wide.js'))
        .pipe(gulp.dest(jsDest))
        .pipe(rename('site-wide.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(jsDest))
        .pipe(notify({message: 'Scripts finished.'}));
});

gulp.task('watch', function () {
    gulp.watch('./assets/styles/**/*.scss', ['sass']);
    gulp.watch('assets/styles/editor-style.scss', ['editor-sass']);
    gulp.watch('./assets/icons/src/*.svg', ['svgstore']);
    gulp.watch('./assets/js/*.js', ['scripts']);
});

gulp.task('default', ['sass', 'editor-sass', 'svgstore', 'scripts', 'watch']);
