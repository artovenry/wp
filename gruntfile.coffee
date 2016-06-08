module.exports= (grunt)->
  PROJECT_NAME= "artovenry wp"
  HOSTNAME= "192.168.1.2"
  PORT= 3000
  PORT_LIVERELOAD= 30000

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
          'dev/theme/**'
        ]
        livereload: enabled: yes, extensions: [
          'php', 'haml', 'html'
        ]
      "php": (path)->
        ["test"] unless path.match /^dev\//
    shell:
      phpunit:
        command: "phpunit --bootstrap test/bootstrap.php --configuration test/phpunit.xml"
        options:
          failOnError: yes
    php:
      server: options: port:PORT, hostname: '0.0.0.0', base: 'dev/wp', silent: on
    connect:
      front:
        options: port: PORT_LIVERELOAD, hostname: HOSTNAME, livereload: yes, open: no, middleware: ->
          [require('grunt-connect-proxy/lib/utils').proxyRequest]
      proxies: [context: "/", host: HOSTNAME, port:PORT]

  require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks)
  grunt.task.run 'notify_hooks'

  grunt.registerTask 'test',['shell:phpunit']
  grunt.registerTask 'server', [
    'php:server'
    'configureProxies'
    'connect:front'
    'esteWatch'
  ]
  grunt.registerTask 'default', [
    'server'
    'test'
  ]
