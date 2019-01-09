const webpack = require("webpack");
const WebpackBar = require("webpackbar");
const Encore = require("@symfony/webpack-encore");
const path = require("path");

Encore.addPlugin(
  new WebpackBar({
    profile: Encore.isProduction() ? true : false,
    minimal: false
  })
)

  .addPlugin(
    new webpack.ProvidePlugin({
      $bus: [path.resolve(__dirname, "./assets/js/bus/"), "default"]
    })
  )

  .setOutputPath("public/assets/")
  .setPublicPath("/assets")
  .setManifestKeyPrefix("assets")
  .copyFiles({
    from: "./assets/static"
  })
  .copyFiles({
    from: "./node_modules/flagpack/flags",
    to: "icons/flags/[name].[ext]",
    pattern: /\.(svg)$/
  })

  .cleanupOutputBeforeBuild()
  .disableSingleRuntimeChunk()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

  .addEntry("bolt", "./assets/js/bolt.js")
  .addStyleEntry("theme-default", "./assets/scss/themes/default.scss")
  .addStyleEntry("theme-light", "./assets/scss/themes/light.scss")
  .addStyleEntry("theme-dark", "./assets/scss/themes/dark.scss")
  .addStyleEntry("theme-woordpers", "./assets/scss/themes/woordpers.scss")

  .splitEntryChunks()
  .autoProvidejQuery()
  .enableVueLoader()
  .enableSassLoader()
  .enablePostCssLoader();

module.exports = Encore.getWebpackConfig();
