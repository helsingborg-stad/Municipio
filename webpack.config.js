require('dotenv').config();

const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const autoprefixer = require('autoprefixer');
const fs = require('fs');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

module.exports = {
    mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
    entry: {
        'css/styleguide': './assets/source/3.0/sass/styleguide.scss',
        'js/styleguide': './assets/source/3.0/js/styleguide.js',
        'css/municipio': './assets/source/3.0/sass/main.scss',
        'js/municipio': './assets/source/3.0/js/municipio.js',
        'js/instantpage': './node_modules/instant.page/instantpage.js',
        'js/mce': './assets/source/3.0/mce-js/mce-buttons.js',
        'js/mce-table': './assets/source/3.0/mce-js/mce-table.js',
        'css/mce': './assets/source/3.0/sass/mce.scss',
        'css/blockeditor': './assets/source/3.0/sass/blockeditor.scss',
        'js/pdf': './assets/source/3.0/js/pdf.ts',

        /* Admin js */
        'js/color-picker': './assets/source/3.0/js/admin/colorPicker.js',
        'js/design-share': './assets/source/3.0/js/admin/designShare.ts',
        'js/customizer-preview': './assets/source/3.0/js/admin/customizerPreview.js',
        'js/customizer-flexible-header': './assets/source/3.0/js/admin/customizerFlexibleHeader.ts',
        'js/hidden-post-status-conditional': './assets/source/3.0/js/admin/acf/hiddenPostStatusConditional.ts',
        'js/user-group-visibility': './assets/source/3.0/js/admin/private/userGroupVisibility.ts',
        'js/widgets-area-hider': './assets/source/3.0/js/admin/widgetsAreaHider.js',
        'js/customizer-error-handling': './assets/source/3.0/js/admin/customizerErrorHandling.ts',
        'js/blocks/columns': './assets/source/3.0/js/admin/blocks/columns.js',
        'js/event-source-progress': './assets/source/3.0/js/admin/eventSourceProgress/index.ts',

        /* Admin css */
        'css/acf': './assets/source/3.0/sass/admin/acf.scss',
        'css/header-flexible': './assets/source/3.0/sass/admin/header-flexible.scss',
        'css/general': './assets/source/3.0/sass/admin/general.scss',

        /* Login css */
        'css/login': './assets/source/3.0/sass/admin/login.scss',

        /* Legacy 2.0  */
        'js/mce-pricons': './assets/source/3.0/mce-js/mce-pricons.js',
        'js/mce-metadata': './assets/source/3.0/mce-js/mce-metadata.js',

        /* Icons */
        'fonts/material/light/sharp': './assets/source/3.0/sass/icons/light/sharp.scss',
        'fonts/material/light/outlined': './assets/source/3.0/sass/icons/light/outlined.scss',
        'fonts/material/light/rounded': './assets/source/3.0/sass/icons/light/rounded.scss',

        'fonts/material/medium/sharp': './assets/source/3.0/sass/icons/medium/sharp.scss',
        'fonts/material/medium/outlined': './assets/source/3.0/sass/icons/medium/outlined.scss',
        'fonts/material/medium/rounded': './assets/source/3.0/sass/icons/medium/rounded.scss',

        'fonts/material/bold/sharp': './assets/source/3.0/sass/icons/bold/sharp.scss',
        'fonts/material/bold/outlined': './assets/source/3.0/sass/icons/bold/outlined.scss',
        'fonts/material/bold/rounded': './assets/source/3.0/sass/icons/bold/rounded.scss',
    },
    output: {
        filename: process.env.NODE_ENV === 'production' ? '[name].[contenthash:8].js' : '[name].js',
        path: path.resolve(__dirname, 'assets', 'dist'),
        publicPath: '',
    },
    externals: {
        jquery: 'jQuery',
        tinymce: 'tinymce'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'],
                        plugins: [
                            '@babel/plugin-syntax-dynamic-import',
                            '@babel/plugin-proposal-export-default-from',
                            '@babel/plugin-proposal-class-properties',
                        ],
                    }
                }
            },
            {
                test: /\.ts?$/,
                loader: 'ts-loader',
                options: { allowTsInNodeModules: true }
            },
            {
                test: /\.(woff(2)?|ttf|eot|svg|otf)$/,
                type: 'asset/resource',
                generator: {
                    filename: (pathData) => {
                        const resourcePath = pathData.path || pathData.filename || '';
                        const weightMap = {
                            'font-200': 'light',
                            'font-400': 'medium',
                            'font-600': 'bold',
                        };
                        const dirNames = path.dirname(resourcePath).split(path.sep);
                        const weightKey = dirNames.find((part) => weightMap[part]);
                        const weight = weightMap[weightKey] || 'unknown';
                        const baseName = path.basename(resourcePath, path.extname(resourcePath));
                        const transformedName = baseName.replace(/^material-symbols-/, '');
                        return `fonts/material/${weight}/${transformedName}.[contenthash:8][ext]`;
                    },
                    publicPath: '',
                }
            },
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [autoprefixer],
                            }
                        },
                    },
                    'sass-loader'
                ],
            },
            {
                test: /\?raw$/,
                use: [
                    {
                        loader: 'raw-loader',
                        options: {
                            esModule: false,
                        }
                    }
                ]
            },
            {
                test: /\.(png|svg|jpg|gif)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: process.env.NODE_ENV === 'production' ? '[name].[contenthash:8].[ext]' : '[name].[ext]',
                            outputPath: 'images',
                            publicPath: '../images',
                        },
                    },
                ],
            },
        ]
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.js'],
    },
    plugins: [
        new CleanWebpackPlugin(),
        new MiniCssExtractPlugin({
            filename: process.env.NODE_ENV === 'production' ? '[name].[contenthash:8].css' : '[name].css',
        }),
        process.env.BROWSER_SYNC_PROXY_URL ? new BrowserSyncPlugin(
            {
                host: 'localhost',
                port: process.env.BROWSER_SYNC_PORT || 3000,
                proxy: process.env.BROWSER_SYNC_PROXY_URL,
                injectCss: true,
                injectChanges: true,
                files: [
                    { match: ['views/**/*.blade.php', 'library/**/*.php', 'assets/dist/js/**/*.js'] },
                    { match: ['assets/dist/css/**/*.css'] }
                ]
            }
        ) : null,
        new webpack.ProvidePlugin({
            process: 'process/browser',
        }),

        new WebpackManifestPlugin({
            // Filter manifest items
            filter: function (file) {
                // Don't include source maps
                if (file.path.match(/\.(map)$/)) {
                    return false;
                }
                if (file.path.match(/\.(woff2)$/)) {
                    return false;
                }
                return true;
            },
            // Custom mapping of manifest item goes here
            map: function (file) {
                return file;
            },
        }),
        /** Parse the icon specification in material-symbols, make json */
        function () {
            const filePath = path.resolve(__dirname, 'node_modules', 'material-symbols', 'index.d.ts');

            fs.readFile(filePath, 'utf8', (err, data) => {
                if (err || !data) {
                    console.error(err ? `Error reading icon file: ${filePath} [${err}]` : `No data in icon file: ${filePath}`);
                    return;
                }

                const [startIndex, endIndex] = [
                    data.indexOf('['),
                    data.indexOf(']')
                ];

                if (startIndex === -1 || endIndex === -1) {
                    console.error('Could not parse source file. Source file malformed.');
                    return;
                }

                let iconArray = [];
                try {
                    iconArray = JSON.parse(data.substring(startIndex, endIndex + 1));
                } catch (parseError) {
                    console.error(`Error parsing icon data: ${parseError}`);
                    return;
                }

                const json = JSON.stringify(iconArray, null, 2);
                const resultDirectory = path.resolve(__dirname, 'assets', 'generated');
                const resultFilepath = path.resolve(resultDirectory, 'icon.json');

                try {
                    fs.mkdirSync(resultDirectory, { recursive: true });
                    fs.writeFileSync(resultFilepath, json);
                } catch (err) {
                    console.error(err);
                }
            })
        }
    ].filter(Boolean),
    devtool: 'source-map',
    stats: { children: false },
};
