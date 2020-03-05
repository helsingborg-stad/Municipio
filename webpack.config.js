const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
const path = require('path');
const glob = require('glob');
const StylelintPlugin = require('stylelint-webpack-plugin');

module.exports = {
    // ...
    externals: {
        material: '@material'
    },
    
    /**
     * Entry files - Add more entries if needed.
     */
    entry: {
        'styleguide-js': glob.sync('./source/js/**/*.js')
    },
    mode: 'development',
    watch: true,
    watchOptions: {
        poll: 100,
        ignored: /node_modules/
    },
    
    /**
     * Output files
     */
    output: {
        path: path.resolve(__dirname, 'assets/dist/'),
        filename: 'js/[name].min.js'
    },
    
    /**
     * Modules
     */
    module: {
        rules: [
            
            /**
             * Babel
             */
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            
            /**
             * Fonts - File loader
             */
            {
                test: /\.(woff|woff2|ttf|otf|eot|svg)$/,
                use: [
                    {
                        loader: "file-loader",
                        options: {
                            outputPath: 'fonts'
                        }
                    }
                ]
            }
        ]
    },
    
    /**
     * Plugins
     */
    plugins: [
        
        // Prevent Webpack to create javascript css
        new FixStyleOnlyEntriesPlugin(),
        // Lint for scss
        new StylelintPlugin({
            context: "./source/sass",
            configFile: "./.stylelintrc",
            emitWarning: true,
            defaultSeverity: "warning"
        })
    ]
    // ...
};