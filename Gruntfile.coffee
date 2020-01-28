module.exports = (grunt) ->
  sass = require 'node-sass'
  @initConfig
    pkg: @file.readJSON('package.json')
    release:
      branch: ''
      repofullname: ''
      lasttag: ''
      msg: ''
      post: ''
      url: ''
    watch:
      files: [
        'css/src/*.scss'
      ]
      tasks: ['sasslint', 'sass:dev']
    postcss:
      pkg:
        options:
          processors: [
            require('autoprefixer')({browsers: ['last 2 versions','ie > 9']})
          ]
          failOnError: true
        files:
          'css/college.css': 'css/college.css'
      dev:
        options:
          map: true
          processors: [
            require('autoprefixer')({browsers: ['last 2 versions','ie > 9']})
          ]
          failOnError: true
        files:
          'css/college.css': 'css/college.css'
    sass:
      pkg:
        options:
          implementation: sass
          noSourceMap: true
          outputStyle: 'compressed'
          precision: 2
          includePaths: ['node_modules/foundation-sites/scss']
        files:
          'css/college.css': 'css/src/college.scss'
      dev:
        options:
          implementation: sass
          sourceMap: true
          includePaths: ['node_modules/foundation-sites/scss']
          outputStyle: 'nested'
          precision: 2
        files:
          'css/college.css': 'css/src/college.scss'
    sasslint:
      options:
        configFile: '.sass-lint.yml'
      target: ['css/**/*.s+(a|c)ss']
    compress:
      main:
        options:
          archive: '<%= pkg.name %>.zip'
        files: [
          {src: ['css/*.css']},
          {src: ['src/*.php']},
          {src: ['*.php']},
          {src: ['readme.md']},
        ]
    concat:
      dist:
        options:
          stripBanners: true
          separator: '\n'
          process: (src, filepath) ->
            return src.replace(/\n\/\/# sourceMappingURL=[^\n]+\n?/g, '')
        src: [
          'node_modules/foundation-sites/dist/js/plugins/foundation.toggler.min.js',
        ]
        dest: 'js/foundation.concat.js'
      dev:
        options:
          sourceMap: true
        src: [
          'node_modules/foundation-sites/dist/js/plugins/foundation.toggler.js'
        ]
        dest: 'js/foundation.concat.js'

  @loadNpmTasks 'grunt-contrib-watch'
  @loadNpmTasks 'grunt-contrib-compress'
  @loadNpmTasks 'grunt-contrib-concat'
  @loadNpmTasks 'grunt-sass-lint'
  @loadNpmTasks 'grunt-sass'
  @loadNpmTasks 'grunt-postcss'

  @registerTask 'default', ['sass:pkg', 'concat:dist', 'postcss:pkg']
  @registerTask 'develop', ['sasslint', 'concat:dev', 'sass:dev', 'postcss:dev']
  @registerTask 'release', ['compress', 'makerelease']
  @registerTask 'makerelease', 'Set release branch for use in the release task', ->
    done = @async()

    # Define simple properties for release object
    grunt.config 'release.key', process.env.RELEASE_KEY
    grunt.config 'release.file', grunt.template.process '<%= pkg.name %>.zip'
    grunt.config 'release.msg', grunt.template.process 'Upload <%= pkg.name %>.zip to your dashboard.'

    grunt.util.spawn {
      cmd: 'git'
      args: [ 'rev-parse', '--abbrev-ref', 'HEAD' ]
    }, (err, result, code) ->
      if result.stdout isnt ''
        matches = result.stdout.match /([^\n]+)$/
        grunt.config 'release.branch', matches[1]
        grunt.task.run 'setrepofullname'

      done(err)
      return
    return
  @registerTask 'setrepofullname', 'Set repo full name for use in the release task', ->
    done = @async()

    # Get repository owner and name for use in Github REST requests
    grunt.util.spawn {
      cmd: 'git'
      args: [ 'config', '--get', 'remote.origin.url' ]
    }, (err, result, code) ->
      if result.stdout isnt ''
        grunt.log.writeln 'Remote origin url: ' + result
        matches = result.stdout.match /([^\/:]+)\/([^\/.]+)(\.git)?$/
        grunt.config 'release.repofullname', matches[1] + '/' + matches[2]
        grunt.task.run 'setpostdata'

      done(err)
      return
    return
  @registerTask 'setpostdata', 'Set post object for use in the release task', ->
    val =
      tag_name: 'v' + grunt.config.get 'pkg.version'
      name: grunt.template.process '<%= pkg.name %> (v<%= pkg.version %>)'
      body: grunt.config.get 'release.msg'
      draft: false
      prerelease: false
    grunt.config 'release.post', JSON.stringify val
    grunt.log.write JSON.stringify val

    grunt.task.run 'createrelease'
    return
  @registerTask 'createrelease', 'Create a Github release', ->
    done = @async()

    # Create curl arguments for Github REST API request
    args = ['-X', 'POST', '--url']
    args.push grunt.template.process 'https://api.github.com/repos/<%= release.repofullname %>/releases?access_token=<%= release.key %>'
    args.push '--data'
    args.push grunt.config.get 'release.post'
    grunt.log.write 'curl args: ' + args

    # Create Github release using REST API
    grunt.util.spawn {
      cmd: 'curl'
      args: args
    }, (err, result, code) ->
      grunt.log.write '\nResult: ' + result + '\n'
      grunt.log.write 'Error: ' + err + '\n'
      grunt.log.write 'Code: ' + code + '\n'

      if result.stdout isnt ''
        obj = JSON.parse result.stdout
        # Check for error from Github
        if 'errors' of obj and obj['errors'].length > 0
          grunt.fail.fatal 'Github Error'
        else
          # We need the resulting "release" ID value before we can upload the .zip file to it.
          grunt.config 'release.id', obj.id
          grunt.task.run 'uploadreleasefile'

      done(err)
      return
    return
  @registerTask 'uploadreleasefile', 'Upload a zip file to the Github release', ->
    done = @async()

    # Create curl arguments for Github REST API request
    args = ['-X', 'POST', '--header', 'Content-Type: application/zip', '--upload-file']
    args.push grunt.config.get 'release.file'
    args.push '--url'
    args.push grunt.template.process 'https://uploads.github.com/repos/<%= release.repofullname %>/releases/<%= release.id %>/assets?access_token=<%= release.key %>&name=<%= release.file %>'
    grunt.log.write 'curl args: ' + args

    # Upload Github release asset using REST API
    grunt.util.spawn {
      cmd: 'curl'
      args: args
    }, (err, result, code) ->
      grunt.log.write '\nResult: ' + result + '\n'
      grunt.log.write 'Error: ' + err + '\n'
      grunt.log.write 'Code: ' + code + '\n'

      if result.stdout isnt ''
        obj = JSON.parse result.stdout
        # Check for error from Github
        if 'errors' of obj and obj['errors'].length > 0
          grunt.fail.fatal 'Github Error'

      done(err)
      return
    return

  @event.on 'watch', (action, filepath) =>
    @log.writeln('#{filepath} has #{action}')
