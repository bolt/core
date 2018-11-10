var webpack = require('webpack');
var WebpackBar = require('webpackbar');
var Encore = require('@symfony/webpack-encore');
var path = require('path');

Encore

  .addPlugin(new WebpackBar({
    profile: Encore.isProduction() ? true:false,
    minimal: false
  }))

  // .addPlugin(new webpack.ProvidePlugin({
  //   $bus: [path.resolve(__dirname, './assets/js/bus/'), 'default']
  // }))

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')
  .setManifestKeyPrefix('assets')
  .copyFiles({ from: './assets/static' })

  .cleanupOutputBeforeBuild()
  .enableSingleRuntimeChunk()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .addEntry('bolt', './assets/js/bolt.js')
  .addStyleEntry('theme-default', './assets/scss/themes/default.scss')
  .addStyleEntry('theme-light', './assets/scss/themes/light.scss')

  .splitEntryChunks()
  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader()

;

module.exports =  Encore.getWebpackConfig();


