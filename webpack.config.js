const path = require('path');
const symlinkDir = require('symlink-dir')
const WebpackOnBuildPlugin = require('on-build-webpack');
const webpack = require('webpack');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

//https://www.npmjs.com/package/symlink-dir
//https://www.npmjs.com/package/on-build-webpack

module.exports = {
  mode: 'production',
  entry: './resources/js/index.js',
  output: {
    filename: 'app.js',
    path: path.resolve(__dirname, 'pub/js')
  },
  module: {
    rules: [{
      test: /\.s[c|a]ss$/,
      use: [MiniCssExtractPlugin.loader,
        { loader: 'css-loader', options: { url: false, sourceMap: true } },
        { loader: 'sass-loader', options: { sourceMap: true } }
      ]
    },{
      test: /\.js$/,
      exclude: /node_modules/,
      use: {
        loader: "babel-loader"
      }
    }],
  },
  plugins: [
    new WebpackOnBuildPlugin(function(stats) {
      symlinkDir('resources/img', 'pub/img').then(result => { return symlinkDir('resources/img', 'pub/img') }).catch(err => console.error(err))
    }),
    new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
        "window.jQuery": "jquery"
    }),
    new MiniCssExtractPlugin({
      filename: '../../pub/css/app.css',
    }),
  ]
};
