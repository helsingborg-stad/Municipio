require('dotenv').config();

const path = require('path');

const webpack = require('webpack');
const ManifestPlugin = require('webpack-manifest-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
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
    },
    /**
     * Output settings
     */
    output: {
        filename: ifProduction('[name].[contenthash].js', '[name].[contenthash].js'),
        path: path.resolve(__dirname, 'assets', 'dist'),
    },
    /**
     * Define external dependencies here
     */
    externals: {
        jquery: 'jQuery'
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
                            sourceMap: true,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            plugins: [autoprefixer, require('postcss-object-fit-images')],
                            sourceMap: true,
                        },
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                        }
                    },
                    'import-glob-loader'
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
                            name: ifProduction('[name].[contenthash:8].[ext]', '[name].[contenthash:8].[ext]'),
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
                            name: ifProduction('[name].[contenthash:8].[ext]', '[name].[contenthash:8].[ext]'),
                            outputPath: 'fonts',
                            publicPath: '../fonts',
                        },
                    },
                ],
            }
        ],
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
              proxy: process.env.BROWSER_SYNC_PROXY_URL
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
        new FixStyleOnlyEntriesPlugin(),

        /**
         * Clean dist folder
         */
        new CleanWebpackPlugin(),

        /**
         * Output CSS files
         */
        new MiniCssExtractPlugin({
            filename: ifProduction('[name].[contenthash:8].css', '[name].[contenthash:8].css')
        }),

        /**
         * Output manifest.json for cache busting
         */
        new ManifestPlugin({
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

        /**
         * Required to enable sourcemap from node_modules assets
         */
        new webpack.SourceMapDevToolPlugin(),

        /**
         * Enable build OS notifications (when using watch command)
         */
        new WebpackNotifierPlugin({alwaysNotify: true, skipFirstNotification: true}),

        /**
         * Minimize CSS assets
         */
        ifProduction(new OptimizeCssAssetsPlugin({
            cssProcessorPluginOptions: {
                preset: ['default', { discardComments: { removeAll: true } }],
            },
        }))
    ]).filter(Boolean),
    devtool: ifProduction('none', 'eval-source-map'),
    stats: { children: false }
};
