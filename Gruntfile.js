/* global module */
module.exports = function( grunt ) {

	grunt.initConfig( {

		// Get json file from the google-fonts API
		http: {
			'google-fonts-alpha': {
				options: { url: 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=AIzaSyCDiOc36EIOmwdwspLG3LYwCg9avqC5YLs' },
				dest: 'assets/kirki/modules/webfonts/webfonts-alpha.json'
			},
			'google-fonts-popularity': {
				options: { url: 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=AIzaSyCDiOc36EIOmwdwspLG3LYwCg9avqC5YLs' },
				dest: 'assets/kirki/modules/webfonts/webfonts-popularity.json'
			},
			'google-fonts-trending': {
				options: { url: 'https://www.googleapis.com/webfonts/v1/webfonts?sort=trending&key=AIzaSyCDiOc36EIOmwdwspLG3LYwCg9avqC5YLs' },
				dest: 'assets/kirki/modules/webfonts/webfonts-trending.json'
			}
		},

		// Compile CSS
		sass: {
			dist: {
				files: {
					'assets/kirki/assets/vendor/selectWoo/kirki.css': 'assets/kirki/assets/vendor/selectWoo/kirki.scss',
					'assets/kirki/modules/tooltips/tooltip.css': 'assets/kirki/modules/tooltips/tooltip.scss',
					'assets/kirki/modules/custom-sections/sections.css': 'assets/kirki/modules/custom-sections/sections.scss',
					'assets/kirki/controls/css/styles.css': 'assets/kirki/controls/scss/styles.scss'
				}
			}
		},

		// Check JS syntax
		jscs: {
			src: [
				'Gruntfile.js',
				'assets/kirki/controls/**/*.js',
				'assets/kirki/modules/**/*.js',
				'!assets/kirki/assets/vendor/*'
			],
			options: {
				config: '.jscsrc',
				verbose: true
			}
		},

		// Watch task (run with "grunt watch")
		watch: {
			css: {
				files: [
					'assets/kirki/assets/**/*.scss',
					'assets/kirki/controls/scss/*.scss',
					'assets/kirki/modules/**/*.scss'
				],
				tasks: [ 'sass' ]
			},
			scripts: {
				files: [
					'Gruntfile.js',
					'assets/kirki/controls/js/src/*.js',
					'assets/kirki/modules/**/*.js'
				],
				tasks: [ 'concat', 'uglify' ]
			}
		},

		concat: {
			options: {
				separator: ''
			},
			dist: {
				src: [
					'assets/kirki/controls/js/src/set-setting-value.js',
					'assets/kirki/controls/js/src/kirki.js',
					'assets/kirki/controls/js/src/kirki.control.js',
					'assets/kirki/controls/js/src/kirki.input.js',
					'assets/kirki/controls/js/src/kirki.setting.js',
					'assets/kirki/controls/js/src/kirki.util.js',
					'assets/kirki/controls/js/src/dynamic-control.js',

					'assets/kirki/controls/js/src/background.js',
					'assets/kirki/controls/js/src/color-palette.js',
					'assets/kirki/controls/js/src/dashicons.js',
					'assets/kirki/controls/js/src/date.js',
					'assets/kirki/controls/js/src/dimension.js',
					'assets/kirki/controls/js/src/dimensions.js',
					'assets/kirki/controls/js/src/editor.js',
					'assets/kirki/controls/js/src/multicheck.js',
					'assets/kirki/controls/js/src/multicolor.js',
					'assets/kirki/controls/js/src/number.js',
					'assets/kirki/controls/js/src/palette.js',
					'assets/kirki/controls/js/src/radio-buttonset.js',
					'assets/kirki/controls/js/src/radio-image.js',
					'assets/kirki/controls/js/src/repeater.js',
					'assets/kirki/controls/js/src/slider.js',
					'assets/kirki/controls/js/src/sortable.js',
					'assets/kirki/controls/js/src/switch.js',
					'assets/kirki/controls/js/src/toggle.js',
					'assets/kirki/controls/js/src/typography.js'
				],
				dest: 'assets/kirki/controls/js/script.js'
			}
		},

		uglify: {
			dev: {
				options: {
					mangle: {
						reserved: [ 'jQuery', 'wp', '_' ]
					}
				},
				files: [ {
					expand: true,
					src: [ 'assets/kirki/controls/js/*.js', '!assets/kirki/controls/js/*.min.js' ],
					dest: '.',
					cwd: '.',
					rename: function( dst, src ) {
						return dst + '/' + src.replace( '.js', '.min.js' );
					}
				} ]
			}
		}
	} );

	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-http' );
	grunt.loadNpmTasks( 'grunt-jscs' );

	grunt.registerTask( 'dev', [ 'sass', 'jscs', 'watch' ] );
	grunt.registerTask( 'googlefontsProcess', function() {
		var alphaFonts,
			popularityFonts,
			trendingFonts,
			finalObject = {
				items: {},
				order: {
					popularity: [],
					trending: []
				}
			},
			finalJSON,
			i,
			fontFiles = {};
			fontNames = [];

		// Get file contents.
		alphaFonts      = grunt.file.readJSON( 'assets/kirki/modules/webfonts/webfonts-alpha.json' );
		popularityFonts = grunt.file.readJSON( 'assets/kirki/modules/webfonts/webfonts-popularity.json' );
		trendingFonts   = grunt.file.readJSON( 'assets/kirki/modules/webfonts/webfonts-trending.json' );

		// Populate the fonts.
		for ( i = 0; i < alphaFonts.items.length; i++ ) {
			finalObject.items[ alphaFonts.items[ i ].family ] = {
				family: alphaFonts.items[ i ].family,
				category: alphaFonts.items[ i ].category,
				variants: alphaFonts.items[ i ].variants.sort()

				/* Deprecated
				subsets: alphaFonts.items[ i ].subsets.sort(),
				files: alphaFonts.items[ i ].files
				*/
			};
		}

		// Add the popularity order.
		for ( i = 0; i < popularityFonts.items.length; i++ ) {
			finalObject.order.popularity.push( popularityFonts.items[ i ].family );
			fontNames.push( popularityFonts.items[ i ].family );
		}

		// Add the rrending order.
		for ( i = 0; i < trendingFonts.items.length; i++ ) {
			finalObject.order.trending.push( trendingFonts.items[ i ].family );
		}

		// Generate the font-files object.
		for ( i = 0; i < popularityFonts.items.length; i++ ) {
			fontFiles[ popularityFonts.items[ i ].family ] = popularityFonts.items[ i ].files;
		}

		// Write the final object to json.
		finalJSON = JSON.stringify( finalObject );
		grunt.file.write( 'assets/kirki/modules/webfonts/webfonts.json', finalJSON );
		grunt.file.write( 'assets/kirki/modules/webfonts/webfont-names.json', JSON.stringify( fontNames ) );
		grunt.file.write( 'assets/kirki/modules/webfonts/webfont-files.json', JSON.stringify( fontFiles ) );

		// Delete files no longer needed.
		grunt.file.delete( 'assets/kirki/modules/webfonts/webfonts-alpha.json' ); // jshint ignore:line
		grunt.file.delete( 'assets/kirki/modules/webfonts/webfonts-popularity.json' ); // jshint ignore:line
		grunt.file.delete( 'assets/kirki/modules/webfonts/webfonts-trending.json' ); // jshint ignore:line
	} );
	grunt.registerTask( 'googlefonts', function() {
		grunt.task.run( 'http' );
		grunt.task.run( 'googlefontsProcess' );
	} );
	grunt.registerTask( 'default', [ 'sass:dist', 'concat', 'uglify' ] );
	grunt.registerTask( 'all', [ 'default', 'googlefonts' ] );
};
