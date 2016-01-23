module.exports= (grunt)->
  PROJECT_NAME= "artovenry wp"
  HOSTNAME= "showtarow.local"
  PORT= 3000
  PORT_LIVERELOAD= 30000
  THEME_PATH= "theme"
  ASSETS_PATH= "#{THEME_PATH}/assets"
  JS_PATH= "#{ASSETS_PATH}/js"
  CSS_PATH= "#{ASSETS_PATH}/css"

  grunt.initConfig
    pkg: grunt.file.readJSON 'package.json'
    notify_hooks:
      options: enabled: on, success: on, title: PROJECT_NAME
    esteWatch:
      options:
        dirs: [
          'lib/**/'
          'test/tests/**'
          'test/lib/**'
        ]
        livereload: enabled: yes, extensions: [
          'php', 'haml', 'html'
        ]
      "php": ->["test"]
    shell:
      phpunit:
        command: "phpunit --bootstrap test/bootstrap.php --configuration test/phpunit.xml"
        options:
          failOnError: yes

  require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks)
  grunt.task.run 'notify_hooks'

  grunt.registerTask 'test',['shell:phpunit']
  grunt.registerTask 'default', [
    'test'
    'esteWatch'
  ]
