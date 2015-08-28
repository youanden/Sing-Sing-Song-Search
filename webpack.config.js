var path = require("path");
var webpack = require("webpack");
var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
    entry: "./resources/assets/js/app.js",
    output: {
        path: __dirname,
        filename: "./public/js/app.js"
    },
    resolve: {
        root: [path.join(__dirname, "bower_components")]
    },
    devtool: 'source-map',
    module: {
        loaders: [
            { test: path.join(__dirname, 'resources/assets/js'),
              loader: 'babel-loader' },
            {
              test: /\.scss$/,
              loader: ExtractTextPlugin.extract(
                'css?sourceMap!sass?sourceMap&includePaths[]='
                + path.resolve(
                  __dirname,
                  './bower_components/bootstrap/scss/'
                )
              )
            }
        ]
    },
    plugins: [
        new webpack.DefinePlugin({
          'process.env': {
            NODE_ENV: '"production"'
          }
        }),
        new ExtractTextPlugin('./public/css/app.css'),
        new webpack.ResolverPlugin(
            new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin("bower.json", ["main"])
        )
        //,
        // new webpack.optimize.UglifyJsPlugin({
        //     compress: {
        //         warnings: false
        //     },
        //     mangle: {
        //         except: ['$super', '$', 'exports', 'require']
        //     }
        // })
    ]
}
