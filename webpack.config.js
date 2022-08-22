const WebpackBar = require('webpackbar');
const Encore = require('@symfony/webpack-encore');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

if (!Encore.isProduction()) {
    Encore.addPlugin(new HtmlWebpackPlugin());
    Encore.addPlugin(new CleanWebpackPlugin());
    Encore.configureFilenames({
        css: '[name].[contenthash].css',
        js: '[name].[contenthash].js',
    });
}

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
