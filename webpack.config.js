var WebpackBar = require('webpackbar');
var Encore = require('@symfony/webpack-encore');

Encore

  .addPlugin(new WebpackBar({
    profile: Encore.isProduction() ? true:false,
    minimal: false
  }))

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')
  .setManifestKeyPrefix('assets')
  .copyFiles({ from: './assets/static' })

  .cleanupOutputBeforeBuild()
  .disableSingleRuntimeChunk()
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

module.exports = Encore.getWebpackConfig();


