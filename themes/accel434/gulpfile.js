var gulp = require("gulp");
var rename = require("gulp-rename");
var cheerio = require("gulp-cheerio");

// Sass Plugins
const sass = require("gulp-sass")(require("sass"));
var sourcemaps = require("gulp-sourcemaps");

// Minifiy JS
var concat = require("gulp-concat");
var rename = require("gulp-rename");

// SVG Plugins
var svgmin = require("gulp-svgmin");
var svgstore = require("gulp-svgstore");

let postcss = require("gulp-postcss");
let autoprefixer = require("autoprefixer");
let postcss_scss = require("postcss-scss");

let processors = [autoprefixer];

gulp.task(
  "sass",
  gulp.series(function () {
    return gulp
      .src("assets/styles/*.scss")
      .pipe(sourcemaps.init())
      .pipe(sass())
      .pipe(postcss(processors, { syntax: postcss_scss }))
      .pipe(sourcemaps.write("./assets/maps"))
      .pipe(gulp.dest("./"));
  })
);

gulp.task(
  "editor-sass",
  gulp.series(function () {
    return gulp
      .src("assets/styles/editor-style.scss")
      .pipe(sourcemaps.init())
      .pipe(sass())
      .pipe(postcss(processors, { syntax: postcss_scss }))
      .pipe(sourcemaps.write())
      .pipe(gulp.dest("./"));
  })
);

gulp.task(
  "svgstore",
  gulp.series(function () {
    return (
      gulp
        .src("assets/icons/src/*.svg")
        .pipe(rename({ prefix: "ico-" }))
        //   .pipe(svgmin())
        .pipe(svgstore())
        .pipe(
          cheerio({
            run: function ($) {
              $("[fill]").removeAttr("fill");
            },
            parserOptions: { xmlMode: true },
          })
        )
        .pipe(gulp.dest("assets/icons/dist/"))
    );
  })
);

//script paths
var jsFiles = "assets/js/*.js",
  jsDest = "assets/js/build/";

gulp.task(
  "scripts",
  gulp.series(function () {
    return gulp
      .src(jsFiles)
      .pipe(concat("site-wide.js"))
      .pipe(gulp.dest(jsDest))
      .pipe(rename("site-wide.min.js"))
      .pipe(gulp.dest(jsDest));
  })
);

gulp.task(
  "watch",
  gulp.series(function () {
    gulp.watch("./assets/styles/**/*.scss", gulp.series("sass"));
    gulp.watch("assets/styles/editor-style.scss", gulp.series("editor-sass"));
    gulp.watch("./assets/js/*.js", gulp.series("scripts"));
  })
);

gulp.task(
  "default",
  gulp.series("sass", "editor-sass", "scripts", "svgstore", "watch")
);

gulp.task("build", gulp.series("sass", "editor-sass", "scripts", "svgstore"));
