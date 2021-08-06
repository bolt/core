const WebpackBar = require('webpackbar');
const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .addPlugin(
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
  .addEntry('zxcvbn', './assets/js/zxcvbn.js')
  .addEntry('vibrant', './assets/js/vibrant.js')
  .addStyleEntry('theme-default', './assets/scss/themes/default.scss')
  .addStyleEntry('theme-light', './assets/scss/themes/light.scss')
  .addStyleEntry('theme-dark', './assets/scss/themes/dark.scss')
  .addStyleEntry('theme-woordpers', './assets/scss/themes/woordpers.scss')

  .splitEntryChunks()
  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader()

  .enableVueLoader(() => {}, { runtimeCompilerBuild: true });

module.exports = Encore.getWebpackConfig();
