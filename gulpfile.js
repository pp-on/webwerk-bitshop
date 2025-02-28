const gulp          = require('gulp');
const $             = require('gulp-load-plugins')({
    pattern: ['!gulp-sass', 'gulp-*', 'gulp.*', '@*/gulp{-,.}*'], // the glob(s) to search for
    overridePattern: true
});
const autoprefixer  = require('autoprefixer');
const sourcemaps    = require('gulp-sourcemaps');
const uglify = require('gulp-uglify-es').default;
const sass = require('gulp-sass')(require('node-sass'));

const sassPaths = [
  'node_modules/foundation-sites/scss',
  'node_modules/motion-ui/src'
];

function swallowError(error) {
  // If you want details of the error in the console
  console.log(error.toString())
  this.emit('end')
}

// SASS Compiler + autoprefixer
function scss() {
  return gulp.src(['scss/style.scss'])
    .pipe(sourcemaps.init())
    .pipe(sass({
        includePaths: sassPaths
      })
    .on('error', swallowError))
    .pipe($.postcss([autoprefixer()]))
    .pipe($.cssUrlencodeInlineSvgs())
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('css'));
}

function minify_css() {
  return gulp.src(['css/style.css', 'css/snipcart.min.css'], { allowEmpty: true })
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe($.cleanCss())
    .on('error', swallowError)
    .pipe($.concat('shop.min.css'))
      .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('css'));
};

// JS Minify + rename to *.min.js
function minify_js() {
  return gulp.src(['js/*.js', '!js/*.min.js'], { allowEmpty: true })
    .pipe(sourcemaps.init())
		.pipe($.babel())
    .pipe(uglify())
    .on('error', swallowError)
    .pipe($.rename({
      suffix: '.min'
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('js'));
};

// Watching scss/html files
function serve() {

  gulp.watch("./scss/*.scss", gulp.series(scss, minify_css));
  gulp.watch(["./js/*.js", "!./js/*.min.js"], minify_js);
}

// Tasks
gulp.task('default', gulp.series(scss, minify_css, minify_js, serve));
