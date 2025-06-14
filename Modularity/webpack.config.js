const path = require('path');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScripts = require('webpack-remove-empty-scripts');
const CssMinimizerWebpackPlugin = require('css-minimizer-webpack-plugin');
const { getIfUtils, removeEmpty } = require('webpack-config-utils');
const { ifProduction } = getIfUtils(process.env.NODE_ENV);

module.exports = {
	mode: ifProduction('production', 'development'),

	/**
	 * Add your entry files here
	 */
	entry: {
		'js/modularity-editor-modal': './source/js/modularity-editor-modal.js',
		'js/modularity-text-module': './source/js/modularity-text-module.ts',
		'js/modularity': './source/js/modularity.js',
		'css/modularity': './source/sass/modularity.scss',
		'css/modularity-thickbox-edit': './source/sass/modularity-thickbox-edit.scss',
		'js/user-editable-list': './source/js/private/userEditableList.ts',

		//Admin
		'js/dynamic-map-acf': './source/js/admin/dynamic-map-acf.js',

		'js/block-validation': './source/js/block-validation.ts',
		'js/edit-modules-block-editor': './source/js/edit-modules-block-editor.ts',

		//Modules
		'js/mod-curator-load-more': './source/php/Module/Curator/assets/mod-curator-load-more.js',
		'css/table': './source/php/Module/Table/assets/table.scss',
		'js/video': './source/php/Module/Video/assets/video.js',
		'css/video': './source/php/Module/Video/assets/video.scss',
		'js/ungapd': './source/php/Module/Subscribe/assets/ungapd.ts',
		'js/mod-posts-taxonomy-filtering': './source/php/Module/Posts/assets/taxonomyFiltering.js',
		'css/menu': './source/php/Module/Menu/assets/menu.scss',
		'css/interactive-map': './source/php/Module/InteractiveMap/assets/interactive-map.scss',
		'js/mod-interactive-map': './source/php/Module/InteractiveMap/assets/interactiveMap.ts',
	},

	/**
	 * Output settings
	 */
	output: {
		filename: ifProduction('[name].[contenthash].js', '[name].js'),
		path: path.resolve(__dirname, 'dist'),
		publicPath: '',
	},
	/**
	 * Define external dependencies here
	 */
	externals: {},
	module: {
		rules: [
			/**
			 * Babel
			 */
			{
				test: /\.js?/,
				exclude: /(node_modules|bower_components)/,
				use: {
					loader: 'babel-loader',
					options: {
						// Babel config here
						presets: ['@babel/preset-env', '@babel/preset-react'],
						plugins: [
							'@babel/plugin-syntax-dynamic-import',
							'@babel/plugin-proposal-export-default-from',
							'@babel/plugin-proposal-class-properties',
						],
					},
				},
			},
			/**
			 * Styles
			 */
			{
				test: /\.(sa|sc|c)ss$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							importLoaders: 3, // 0 => no loaders (default); 1 => postcss-loader; 2 => sass-loader
						},
					},
					{
						loader: 'postcss-loader',
						options: {},
					},
					{
						loader: 'sass-loader',
						options: {},
					},
					'import-glob-loader',
				],
			},
			/**
			 * Images
			 */
			{
				test: /\.(png|svg|jpg|gif)$/,
				type: 'asset/resource',
				generator: {
					filename: 'images/action_icons/[name][ext]',
				},
			},
			/**
			* TypeScript
			*/
			{
				test: /\.ts?$/,
				loader: 'ts-loader',
				options: { allowTsInNodeModules: true }
			},
		],
	},
	resolve: {
		extensions: ['.tsx', '.ts', '.js'],
	},
	plugins: removeEmpty([
		/**
		 * BrowserSync
		 */
		typeof process.env.BROWSER_SYNC_PROXY_URL !== 'undefined'
			? new BrowserSyncPlugin(
				// BrowserSync options
				{
					// browse to http://localhost:3000/ during development
					host: 'localhost',
					port: process.env.BROWSER_SYNC_PORT ? process.env.BROWSER_SYNC_PORT : 3000,
					// proxy the Webpack Dev Server endpoint
					// (which should be serving on http://localhost:3100/)
					// through BrowserSync
					proxy: process.env.BROWSER_SYNC_PROXY_URL,
				},
				// plugin options
				{
					// prevent BrowserSync from reloading the page
					// and let Webpack Dev Server take care of this
					reload: false,
				}
			)
			: null,

		/**
		 * Fix CSS entry chunks generating js file
		 */
		new RemoveEmptyScripts(),

		/**
		 * Clean dist folder
		 */
		new CleanWebpackPlugin(),
		/**
		 * Output CSS files
		 */
		new MiniCssExtractPlugin({
			filename: ifProduction('[name].[contenthash:8].css', '[name].css'),
		}),

		/**
		 * Output manifest.json for cache busting
		 */
		new WebpackManifestPlugin({
			// Filter manifest items
			filter: function (file) {
				// Don't include source maps
				if (file.path.match(/\.(map)$/)) {
					return false;
				}
				return true;
			},
			// Custom mapping of manifest item goes here
			map: function (file) {
				// Fix incorrect key for fonts
				if (file.isAsset && file.isModuleAsset && file.path.match(/\.(woff|woff2|eot|ttf|otf)$/)) {
					const pathParts = file.path.split('.');
					const nameParts = file.name.split('.');

					// Compare extensions
					if (pathParts[pathParts.length - 1] !== nameParts[nameParts.length - 1]) {
						file.name = pathParts[0].concat('.', pathParts[pathParts.length - 1]);
					}
				}

				return file;
			},
		}),

		/**
		 * Enable build OS notifications (when using watch command)
		 */
		new WebpackNotifierPlugin({ alwaysNotify: true, skipFirstNotification: true }),

		/**
		 * Minimize CSS assets
		 */
		ifProduction(
			new CssMinimizerWebpackPlugin({
				minimizerOptions: {
					preset: [
						'default',
						{
							discardComments: { removeAll: true },
						},
					],
				},
			})
		),
	]).filter(Boolean),
	devtool: 'source-map',
	stats: { children: false },
};
