var Encore = require('@symfony/webpack-encore');

Encore

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')

  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .setManifestKeyPrefix('assets')
  .addEntry('bolt', './assets/js/bolt.js')

  .addStyleEntry('theme-default', './assets/scss/themes/default.scss')
  .addStyleEntry('theme-dark', './assets/scss/themes/dark.scss')

  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader()

  if(Encore.isProduction()){
    Encore.configureFilenames({
      js: '[name]-[hash:8].min.js',
      css: '[name]-[hash:8].min.css',
    })
  }

  
module.exports = Encore.getWebpackConfig();
