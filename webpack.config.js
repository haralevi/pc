'use strict';

const webpack = require("webpack");
const ExtractTextPlugin = require("extract-text-webpack-plugin");

let PROD = JSON.parse(process.env.PROD_ENV || '0');
//PROD = true;

let cssLoader = 'css?-url!sass';
if(PROD) cssLoader = 'css?-url&minimize!sass';

module.exports = {
    context: __dirname + "/src",
    entry: {
        '/js/js': ["./js/js.js", "./js/css.js"],
    },
    output: {
        filename: "[name].min.js",
        path: __dirname + "/public",
        //publicPath: '/public/',
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                use: [{
                    loader: "babel-loader",
                    options: { presets: ["es2015"] }
                }]
            },
            {
                test: /\.(sass|scss)$/,
                loader: ExtractTextPlugin.extract({loader: cssLoader}),
            },
            /*
            {
                test: /\.(png|jpg|gif)$/, 
                loader: 'raw'
            },
            {
				test: /\.css$/,
				loader: ExtractTextPlugin.extract({
					loader: "css-loader"
				})
			},
            */
            
        ]
    },
    plugins: PROD ? [
        new ExtractTextPlugin({
			filename: "css/css.min.css",
			allChunks: true
		}),
        new webpack.optimize.UglifyJsPlugin({
            compress: { warnings: false }
        }),
        
    ] : [
        new ExtractTextPlugin({
			filename: "css/css.min.css",
			allChunks: true
		}),
    ]
};