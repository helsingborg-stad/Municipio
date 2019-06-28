const path = require('path');
const glob = require('glob');
const webpack = require('webpack');
const ManifestPlugin = require('webpack-manifest-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

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
        'js/app': glob.sync('./assets/source/js/*.js'),
        'js/admin': glob.sync('./assets/source/js/Admin/*.js'),
        'js/ajax': glob.sync('./assets/source/js/Ajax/*.js'),
        'js/mce': glob.sync('./assets/source/mce-js/*.js'),
        'js/mce-metadata': './assets/source/mce-js/mce-metadata.js',
        'js/mce-buttons': './assets/source/mce-js/mce-buttons.js',
        'js/mce-pricons': './assets/source/mce-js/mce-pricons.js',
        'js/mce-print-break': './assets/source/mce-js/mce-print-break.js',

        'css/app': './assets/source/sass/app.scss',
        'css/admin': './assets/source/sass/admin.scss',
        'css/customizer': './assets/source/sass/customizer.scss',
    },

    /**
     * Output settings
     */
    output: {
        filename: ifProduction('[name].[contenthash].js', '[name].js'),
        path: path.resolve(__dirname, 'assets', 'dist'),
    },
    /**
     * Define external dependencies here
     */
    externals: {
        jquery: 'jQuery',
        tinymce: 'tinymce',
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
                            sourceMap: true,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            plugins: [autoprefixer],
                            sourceMap: true,
                        },
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                        },
                    },
                    'import-glob-loader',
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
            },
        ],
    },
    plugins: removeEmpty([
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
            filename: ifProduction('[name].[contenthash:8].css', '[name].css'),
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
            fileName: 'rev-manifest.json',
        }),

        /**
         * Required to enable sourcemap from node_modules assets
         */
        new webpack.SourceMapDevToolPlugin(),

        /**
         * Enable build OS notifications (when using watch command)
         */
        new WebpackNotifierPlugin({ alwaysNotify: true, skipFirstNotification: true }),

        /**
         * Minimize CSS assets
         */
        ifProduction(
            new OptimizeCssAssetsPlugin({
                cssProcessorPluginOptions: {
                    preset: ['default', { discardComments: { removeAll: true } }],
                },
            })
        ),
    ]),
    devtool: ifProduction('source-map', 'eval-source-map'),
    stats: { children: false },
};
