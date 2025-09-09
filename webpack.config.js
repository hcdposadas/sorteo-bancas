const path = require('path');

module.exports = function(env) {
    const isProduction = env === 'production';
    
    return {
        mode: isProduction ? 'production' : 'development',
        entry: "./src/js/app.js",
        output: {
            path: path.resolve(__dirname, "dist"),
            filename: "bundle.js",
            publicPath: 'dist/'
        },
        module: {
            rules: [
                {
                    test: /\.(scss)$/,
                    use: [
                        'style-loader',
                        'css-loader',
                        {
                            loader: 'sass-loader',
                            options: {
                                implementation: require('sass'),
                                sassOptions: {
                                    fiber: false
                                }
                            }
                        }
                    ]
                },
                {
                    test: /\.css$/,
                    use: [
                        'style-loader',
                        { 
                            loader: 'css-loader', 
                            options: { importLoaders: 1 } 
                        },
                        'postcss-loader'
                    ]
                },
                {
                    test: /\.(woff|woff2|eot|ttf|otf|svg)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: 'fonts/[name][ext]'
                    }
                }
            ]
        },
        devtool: isProduction ? 'source-map' : 'eval-source-map',
        stats: {
            errorDetails: true
        }
    };
};