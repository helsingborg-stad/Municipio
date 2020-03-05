const path = require('path');
const glob = require('glob');

module.exports = {
    // ...
    externals: {
        material: '@material'
    },
    
    /**
     * Entry files - Add more entries if needed.
     */
    entry: {
        'styleguide-js': glob.sync('./node_modules/@helsingborg-stad/styleguide/source/**/*.js')
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
        path: path.resolve(__dirname, 'assets/dist/3.0/'),
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
        ]
    },
};