module.exports = function( grunt ) {

	'use strict';

	require( 'load-grunt-tasks' )( grunt );

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		addtextdomain: {
			options: {
				textdomain: 'y4sent',
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: [ '*.php', '**/*.php', '!\.git/**/*', '!bin/**/*', '!node_modules/**/*', '!tests/**/*' ]
			}
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt'
				}
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: [ '\.git/*', 'bin/*', 'node_modules/*', 'tests/*' ],
					mainFile: 'y4sent.php',
					potFilename: 'y4sent.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		watch: {
			files: 'assets/sass/**/*.scss',
			tasks: ['sass']
		},

		sass: {
			options: { sourceMap: false },
			dev: {
				files: {
					'assets/css/y4sent.css': 'assets/sass/y4sent.scss'
				}
			}
		},

		autoprefixer: {
			options: {
				browsers: [
					"Android 2.3",
					"Android >= 4",
					"Chrome >= 20",
					"Firefox >= 24",
					"Explorer >= 8",
					"iOS >= 6",
					"Opera >= 12",
					"Safari >= 6"
				]
			},
			style: {
				src: ['assets/css/y4sent.css']
			}
		},

		cssmin: {
			main: {
				files: {
					'assets/css/y4sent.css': ['assets/css/y4sent.css']
				}
			}
		},
	} );

	grunt.registerTask( 'default', [ 'i18n','readme' ] );
	grunt.registerTask( 'i18n', [ 'addtextdomain', 'makepot' ] );
	grunt.registerTask( 'readme', [ 'wp_readme_to_markdown' ] );
	grunt.registerTask( 'style', [ 'autoprefixer', 'cssmin' ] );

	grunt.util.linefeed = '\n';

};
