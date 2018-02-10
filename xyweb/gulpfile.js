var gulp = require('gulp');
var ngAnnotate = require('gulp-ng-annotate');
var ngmin = require('gulp-ngmin');
var stripDebug = require('gulp-strip-debug');
var concat = require('gulp-concat');
//var minifyCss = require('gulp-minify-css');//尚不考虑css压缩
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var jshint = require('gulp-jshint');
var htmlmin = require('gulp-htmlmin');
var     ngHtml2js = require('gulp-ng-html2js')
// var     concat = require('gulp-concat')


// gulp.task('default', function() {
//   // place code for your default task here
// });
gulp.task('default', ['jshint'], function() {
  gulp.start('minifyjs');
});


gulp.task('build-html', function () {
    return gulp.src(['web/app/src/**/*.html'])
        .pipe(htmlmin())
        .pipe(ngHtml2js({
            moduleName: 'xy'
        }))
        .pipe(concat('template.tpl.js'))
        .pipe(gulp.dest('dist'));
});


gulp.task('testHtmlmin', function () {
    var options = {
        removeComments: true,//清除HTML注释
        collapseWhitespace: true,//压缩HTML
        collapseBooleanAttributes: true,//省略布尔属性的值 <input checked="true"/> ==> <input />
        removeEmptyAttributes: true,//删除所有空格作属性值 <input id="" /> ==> <input />
        removeScriptTypeAttributes: true,//删除<script>的type="text/javascript"
        removeStyleLinkTypeAttributes: true,//删除<style>和<link>的type="text/css"
        minifyJS: true,//压缩页面JS
        minifyCSS: true//压缩页面CSS
    };
    gulp.src('web/app/src/**/*.html')
        .pipe(htmlmin(options))
        .pipe(gulp.dest('dist/html'));
});


gulp.task('minifyjs',function(){
	  return gulp.src(['./web/app/src/SharedService/XY.js','./web/app/src/Main/*.js','./unsupport/**/*.js','./web/app/src/Class/*.js','./web/app/src/SharedService/*.js','./web/app/src/**/*.js'])      //需要操作的文件
    .pipe(concat('main.js'))    //合并所有js到main.js
    .pipe(gulp.dest('./dist'))       //输出到文件夹
    .pipe(rename({suffix: '.min'}))   //rename压缩后的文件名
    .pipe(ngAnnotate())
    .pipe(ngmin({dynamic: false}))//Pre-minify AngularJS apps with ngmin
    .pipe(stripDebug())//除去js代码中的console和debugger输出
    .pipe(uglify({outSourceMap: false}))    //压缩
    .pipe(gulp.dest('./dist'));  //输出
});

gulp.task('jshint', function () {
  return gulp.src(['./unsupport/**/*.js','./web/app/src/**/*.js'])
    .pipe(jshint())
    .pipe(jshint.reporter('default'));
});

var sass = require('gulp-sass');
 
gulp.task('sass', function () {
  return gulp.src('./web/app/scss/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./web/app/scss_css'));
});

gulp.task('sass:watch', function () {
  gulp.watch('./web/app/scss/*.scss', ['sass']);
});
