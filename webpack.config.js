const path = require('path');
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
    .enableSassLoader(options => {
        options.sassOptions = {
            silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'legacy-js-api'],
            quietDeps: true,
        };
    })
    .enablePostCssLoader()
    .enableVueLoader(() => {}, { runtimeCompilerBuild: true })
    .enableTypeScriptLoader();

// Vue 2 only: Pinia 2 reaches Vue through vue-demi, whose default build is the
// Vue 3 one until its postinstall switches it. An install that skips scripts
// (--ignore-scripts, pnpm 10) would keep the Vue 3 build and fail to compile
// against Vue 2.7, so point at the 2.7 build directly. Remove this together
// with the move to Vue 3 and Pinia 3, which no longer uses vue-demi.
Encore.addAliases({
    'vue-demi$': require.resolve('vue-demi/lib/v2.7/index.mjs', {
        paths: [path.dirname(require.resolve('pinia/package.json'))],
    }),
});

module.exports = Encore.getWebpackConfig();
