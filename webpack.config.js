const WebpackBar = require('webpackbar');
const Encore = require('@symfony/webpack-encore');

Encore.addPlugin(
  new WebpackBar({
    profile: Encore.isProduction(),
    minimal: false,
  }),
)

  .setOutputPath('public/assets/')
  .setPublicPath('/assets')
  .setManifestKeyPrefix('assets')

  .copyFiles({
    from: './assets/static',
  })

  .cleanupOutputBeforeBuild()
  .disableSingleRuntimeChunk()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(false)

  .addEntry('bolt', './assets/js/bolt.js')
  .addStyleEntry('theme-default', './assets/scss/themes/default.scss')
  .addStyleEntry('theme-light', './assets/scss/themes/light.scss')
  .addStyleEntry('theme-dark', './assets/scss/themes/dark.scss')
  .addStyleEntry('theme-woordpers', './assets/scss/themes/woordpers.scss')

  .splitEntryChunks()
  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader();

module.exports = Encore.getWebpackConfig();
