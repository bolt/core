var webpack = require('webpack');
var WebpackBar = require('webpackbar');
var Encore = require('@symfony/webpack-encore');
require("@babel/polyfill");

Encore

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')

  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .setManifestKeyPrefix('assets')
  .addEntry('bolt', './assets/js/bolt.js')
  .createSharedEntry('vendor', ['@babel/polyfill'])

  .addStyleEntry('theme-default', './assets/scss/themes/default.scss')

  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader()

  .addPlugin(new WebpackBar())

  if(Encore.isProduction()){
    Encore.configureFilenames({
      js: '[name]-[hash:8].min.js',
      css: '[name]-[hash:8].min.css',
    })
  }
  

  
module.exports = Encore.getWebpackConfig();
