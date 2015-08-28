var path = require("path");
var webpack = require("webpack");
module.exports = {
    entry: "./resources/assets/js/app.js",
    resolve: {
        root: [path.join(__dirname, "bower_components")]
    },
    plugins: [
        new webpack.ResolverPlugin(
            new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin("bower.json", ["main"])
        )
    ]
}
