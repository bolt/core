var webpack = require('webpack');
var WebpackBar = require('webpackbar');
var Encore = require('@symfony/webpack-encore');
require("@babel/polyfill");

Encore

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')

  .cleanupOutputBeforeBuild()
  // .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .setManifestKeyPrefix('assets')
  .addEntry('bolt', './assets/js/bolt.js')
  .createSharedEntry('vendor', ['@babel/polyfill'])

  .addStyleEntry('theme-default', './assets/scss/themes/default.scss')
  .addStyleEntry('theme-light', './assets/scss/themes/light.scss')

  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader()

  .addPlugin(new WebpackBar({
    profile: Encore.isProduction() ? true:false,
    minimal: false
  }))

  if(Encore.isProduction()){
    Encore.configureFilenames({
      js: '[name]-[hash:8].min.js',
      css: '[name]-[hash:8].min.css',
    })
  }
  
module.exports = Encore.getWebpackConfig();

