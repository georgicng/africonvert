var gulp = require('gulp');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var clean = require('gulp-clean');
var filter = require('gulp-filter');
var livereload = require('gulp-livereload');

gulp.task('default', function () {
    // Default task code
    console.log('GULP GULP GULP')
});

gulp.task('scripts', ['move'], function () {
    var jsFiles = 'js/dev/**/*.js',
    jsDest = 'js/app';

    return gulp.src(jsFiles)
        .pipe(concat('all.js'))
        .pipe(gulp.dest(jsDest))
});

gulp.task('filter', ['move'], function () {
    var jsFiles = 'js/dev/**/*.js',
    jsDest = 'js/app',
    controllers = filter('**/*.controller*.js', {restore: true}),
    routes = filter('**/*.route*.js', {restore: true}),
    services = filter('**/*.service*.js', {restore: true})
    modules = filter(['**/app*.js','**/*config.js'], {restore: true});


    return gulp.src(jsFiles)  
        .pipe(modules)
        .pipe(concat('modules.js'))
        .pipe(modules.restore)  
        .pipe(routes)
        .pipe(concat('routes.js'))
        .pipe(routes.restore)
        .pipe(controllers)
        .pipe(concat('controllers.js'))
        .pipe(controllers.restore)
        .pipe(services)
        .pipe(concat('services.js'))
        .pipe(services.restore)
        .pipe(gulp.dest(jsDest));
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch('js/dev/**/*.*', ['combine'])
});

gulp.task('combine',['move'], function () {
    var jsFiles = 'js/dev/**/*.js',
    jsDest = 'js/app';

    return gulp.src(jsFiles)
        .pipe(concat('all.js'))
        .pipe(gulp.dest(jsDest))
        .pipe(livereload());
});

gulp.task('minify', function () {
    var jsFiles = 'js/dev/**/*.js',
    jsDest = 'js/app';

    return gulp.src(jsFiles)
        .pipe(concat('all.js'))
        .pipe(uglify())        
        .pipe(rename('app.min.js'))
        .pipe(gulp.dest(jsDest));
});


gulp.task('clean', function(){
  return gulp.src(['partials/*', 'js/app/*'], {read:false})
  .pipe(clean());
});

gulp.task('move',['clean'], function(){
    var filesToMove = [
        'js/dev/modules/**/*.html'
    ],
    dest = 'partials/';
  // the base option sets the relative root for the set of files,
  // preserving the folder structure
  gulp.src(filesToMove)
  .pipe(rename({dirname: ''}))
  .pipe(gulp.dest(dest));
});