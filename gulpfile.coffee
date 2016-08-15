coffee = require 'gulp-coffee'
sass   = require 'gulp-sass'
chmod  = require 'gulp-chmod'
prefix = require 'gulp-autoprefixer'
concat = require 'gulp-concat'
minifyCSS = require 'gulp-minify-css'
browsersync = require('browser-sync').create()
gulp        = require 'gulp'

#pretty straight forward, edit as necessary
DIR = 
  JS : 
    APP : 'src/coffee/*.coffee'
    OUT : 'public/js/',
  CSS :
    IN : 'src/sass/'
    OUT : 'public/css/'

gulp.task 'sass', ->
  gulp.src DIR.CSS.IN+"*.sass"
    .pipe sass style: "compressed"
    .pipe prefix browsers: ['last 2 versions']
    .pipe minifyCSS compatibility: 'ie8'
    .pipe chmod 644
    .pipe gulp.dest DIR.CSS.OUT
    .pipe browsersync.stream()

gulp.task 'coffee', ->

  gulp.src DIR.JS.APP
    .pipe coffee bare: true
    .pipe concat "app.js"
    .pipe chmod 644
    .pipe gulp.dest DIR.JS.OUT
    .pipe browsersync.stream()

gulp.task 'serve', ->
  browsersync.init {server: {proxy: 'http://localhost:1338'}}
  gulp.watch [DIR.CSS.IN+"*.sass", DIR.CSS.IN+"**/*.sass"], ['sass']
  gulp.watch [DIR.JS.APP], ['coffee']
  gulp.watch("*.html").on('change', browsersync.reload)
  return


gulp.task 'default', ['sass', 'coffee', 'serve']