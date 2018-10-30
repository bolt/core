var Encore = require('@symfony/webpack-encore');

Encore

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')

  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .setManifestKeyPrefix('assets')
  .addEntry('bolt', './assets/js/bolt.js')

  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader()


module.exports = Encore.getWebpackConfig();
