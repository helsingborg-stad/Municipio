require('dotenv').config();

const path = require('path');

const webpack = require('webpack');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScripts = require('webpack-remove-empty-scripts');
const CssMinimizerWebpackPlugin = require('css-minimizer-webpack-plugin');
const autoprefixer = require('autoprefixer');

const { getIfUtils, removeEmpty } = require('webpack-config-utils');
const { ifProduction, ifNotProduction } = getIfUtils(process.env.NODE_ENV);

module.exports = {
    mode: ifProduction('production', 'development'),
    /**
     * Add your entry files here
     */
    entry: {
        'css/styleguide': './assets/source/3.0/sass/styleguide.scss',
        'js/styleguide': './assets/source/3.0/js/styleguide.js',
        'css/municipio': './assets/source/3.0/sass/main.scss',
        'js/municipio': './assets/source/3.0/js/municipio.js',
        'js/instantpage': './node_modules/instant.page/instantpage.js',
        'js/mce': './assets/source/3.0/mce-js/mce-buttons.js',
        'css/mce': './assets/source/3.0/sass/mce.scss',
        'css/blockeditor': './assets/source/3.0/sass/blockeditor.scss',

        /* Admin js */
        'js/color-picker': './assets/source/3.0/js/admin/colorPicker.js',
        'js/design-share': './assets/source/3.0/js/admin/designShare.ts',
        'js/customizer-preview': './assets/source/3.0/js/admin/customizerPreview.js',
        'js/widgets-area-hider': './assets/source/3.0/js/admin/widgetsAreaHider.js',

        'js/blocks/columns': './assets/source/3.0/js/admin/blocks/columns.js',
        
        /* Legacy 2.0  */
        'js/mce-pricons': './assets/source/3.0/mce-js/mce-pricons.js',
        'js/mce-metadata': './assets/source/3.0/mce-js/mce-metadata.js'
    },
    /**
     * Output settings  
     */
    output: {
        filename: ifProduction('[name].[contenthash].js', '[name].js'),
        path: path.resolve(__dirname, 'assets', 'dist'),
        publicPath: '',
    },
    /**
     * Define external dependencies here
     */
    externals: {
        jquery: 'jQuery',
        tinymce: 'tinymce'
    },
    module: {
        rules: [
            /**
             * Scripts
             */
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        // Babel config goes here
                        presets: ['@babel/preset-env'],
                        plugins: [
                            '@babel/plugin-syntax-dynamic-import',
                            '@babel/plugin-proposal-export-default-from',
                            '@babel/plugin-proposal-class-properties',
                        ],
                    }
                }
            },
            /**
             * TypeScript
             */
            {
                test: /\.ts?$/,
                use: 'ts-loader',
                exclude: /node_modules\/(?!(@helsingborg-stad\/styleguide\/source)\/).*/,
            },
            /**
             * Styles
             */
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {}
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            importLoaders: 2
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [autoprefixer, require('postcss-object-fit-images')],
                            }
                        },
                    },
                    {
                        loader: 'sass-loader',
                        options: {}
                    }
                ],
            },

            /**
             * Images
             */
            {
                test: /\.(png|svg|jpg|gif)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: ifProduction('[name].[contenthash:8].[ext]', '[name].[ext]'),
                            outputPath: 'images',
                            publicPath: '../images',
                        },
                    },
                ],
            },

            /**
             * Fonts
             */
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: ifProduction('[name].[contenthash:8].[ext]', '[name].[ext]'),
                            outputPath: 'fonts',
                            publicPath: '../fonts',
                        },
                    },
                ],
            }
        ],
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.js'],
    },
    plugins: removeEmpty([

        /**
         * BrowserSync
         */
        typeof process.env.BROWSER_SYNC_PROXY_URL !== 'undefined' ? new BrowserSyncPlugin(
            // BrowserSync options
            {
                // browse to http://localhost:3000/ during development
                host: 'localhost',
                port: process.env.BROWSER_SYNC_PORT ? process.env.BROWSER_SYNC_PORT : 3000,
                // proxy the Webpack Dev Server endpoint
                // (which should be serving on http://localhost:3100/)
                // through BrowserSync
                proxy: process.env.BROWSER_SYNC_PROXY_URL,
                injectCss: true,
                injectChanges: true,
                files: [{
                  // Reload page
                  match: ['views/**/*.blade.php', 'library/**/*.php', 'assets/dist/js/**/*.js'],
                  fn: function(event, file) {
                    if (event === "change") {
                      const bs = require('browser-sync').get('bs-webpack-plugin');
                      bs.reload();
                    }
                  }
                },
                {
                  // Inject CSS
                  match: ['assets/dist/css/**/*.css'],
                  fn: function(event, file) {
                    if (event === "change") {
                      const bs = require('browser-sync').get('bs-webpack-plugin');
                      bs.reload("*.css");
                    }
                  }
                }],
              },
              // plugin options
              {
                // prevent BrowserSync from reloading the page
                // and let Webpack Dev Server take care of this
                reload: false
              }
        ) : null
        ,

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
            filename: ifProduction('[name].[contenthash:8].css', '[name].css')
        }),

        /**
         * Output manifest.json for cache busting
         */
        new WebpackManifestPlugin({
            // Filter manifest items
            filter: function(file) {
                // Don't include source maps
                if (file.path.match(/\.(map)$/)) {
                    return false;
                }
                return true;
            },
            // Custom mapping of manifest item goes here
            map: function(file) {
                // Fix incorrect key for fonts
                if (
                    file.isAsset &&
                    file.isModuleAsset &&
                    file.path.match(/\.(woff|woff2|eot|ttf|otf)$/)
                ) {
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
        new webpack.ProvidePlugin({
            process: 'process/browser',
        }),
        /**
         * Enable build OS notifications (when using watch command)
         */
        new WebpackNotifierPlugin({alwaysNotify: true, skipFirstNotification: true}),

        /**
         * Minimize CSS assets
         */
        ifProduction(new CssMinimizerWebpackPlugin({
            minimizerOptions: {
                preset: [
                    "default",
                    {
                        discardComments: { removeAll: true },
                    },
                ],
            },
        }))
    ]).filter(Boolean),
    devtool: 'source-map',
    stats: { children: false, loggingDebug: ifNotProduction(['sass-loader'], []), }
};
