let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
        output: {
            path: __dirname + '/public',
            publicPath: "/",
            filename: '[name].js',
            // or whatever other format you want
            chunkFilename: '[name].[id].js',
        },
    })
    .react('resources/assets/js/index.js', 'public/js')

    .copy('node_modules/moment/min/moment.min.js', 'public/js/moment.min.js')

	.copy('resources/assets/images/*', 'public/images')
	
	.sass('resources/assets/sass/app.scss', 'public/css', {
		includePaths: ['node_modules']
	})

    .version();