'use strict';
module.exports = function(grunt) {

	var config = grunt.file.readJSON('grunt-server.config');

	// Load all tasks
	require('load-grunt-tasks')(grunt);
	// Show elapsed time
	require('time-grunt')(grunt);

	var jsFileList = [
		'<%= dir.src.js %>/**/*.js',
		'<%= dir.src.js %>/*.js'
	];

	grunt.util._.extend( config, {
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [ 'Gruntfile.js' ].concat( jsFileList )
		},
		copy: {
			php: {
				files: [
					{ expand: true, cwd: '<%= dir.src.php %>/', src: ['**'], dest: '<%= dir.dist.plugin %>/' }
				]
			},
			js: {
				files: [
					{ expand: true, cwd: '<%= dir.src.js %>/', src: ['**'], dest: '<%= dir.dist.js %>/' }
				]
			},
			sass: {
				files: [
					{ expand: true, cwd: '<%= dir.src.sass %>/', src: ['**'], dest: '<%= dir.dist.sass %>/' }
				]
			}
		},
		sass: {
			options: {
				unixNewlines: true,
				trace: true
			},
			dev: {
				options: {
					style: 'expanded'
				},
				files: [{
					expand: true,
					cwd: '<%= dir.src.sass %>/',
					src: ['*.scss'],
					dest: '<%= dir.dist.css %>',
					ext: '.css'
				}]
			},
			dist: {
				options: {
					style: 'compressed',
					sourcemap: 'none',
					banner: '/* Generated from SASS source files. Do not modify directly. */'
				},
				files: [{
					expand: true,
					cwd: '<%= dir.src.sass %>/',
					src: ['*.scss'],
					dest: '<%= dir.dist.css %>',
					ext: '.css'
				}]
			}
		},
		clean: {
			dist: ['<%= dir.dist.dist %>']
		},
		uglify: {
			dist: {
				options: {
					compress: true,
					preserveComments: 'some',
					banner: '/* Generated from JavaScript source files. Do not modify directly. */\n'
				},
				files: [{
					expand: true,
					cwd: '<%= dir.src.js %>',
					src: '**/*.js',
					dest: '<%= dir.dist.js %>',
					ext: '.min.js'
				}]
			}
		},
		replace : {
	      options : {
	        patterns : [
	          {
	            json: grunt.file.readJSON('grunt-replace-dict.json')
	          }
	        ]
	      },
	      php : {
		      files : [{
		        expand: true, cwd: '<%= dir.src.php %>/', src:['**'], dest: '<%= dir.dist.plugin %>/'
		      }]
		  }
	    },
		flipcss: {
			options: {
				warnings: true
			},
			main: {
				files: [{
					expand: true,
					cwd: '<%= dir.dist.css %>',
					src: '*.css',
					dest: '<%= dir.dist.css %>',
					ext: '-rtl.css'
				}]
			}
		},
		makepot : {
			main: {
				options: {
					cwd: '<%= dir.dist.plugin %>',
					mainFile: 'save-and-then.php',
					type: 'wp-plugin',
					potHeaders: {
						poedit: true
					}
				}
			}
		},
		watch: {
			php: {
				files: [
					'<%= dir.src.php %>/*',
					'<%= dir.src.php %>/**/*'
				],
				tasks: ['copy:php']
			},
			sass: {
				files: [
					'<%= dir.src.sass %>/*.scss',
					'<%= dir.src.sass %>/**/*.scss'
				],
				tasks: ['sass:dev']
			},
			js: {
				files: [
					jsFileList
				],
				tasks: ['jshint', 'copy:js']
			}
		}
	});
	
	grunt.initConfig( config );

	// Register tasks
	grunt.registerTask('default', [
		'dev'
	]);
	grunt.registerTask('dev', [
		'jshint',
		'copy:php',
		'copy:js',
		'sass:dev',
		'flipcss'
	]);
	grunt.registerTask('build', [
		'jshint',
		'clean:dist',
		'replace:php',
		'copy:js',
		'uglify',
		'copy:sass',
		'sass:dist',
		'flipcss'
	]);
};
