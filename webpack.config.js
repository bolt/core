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
    // The `version` option is deliberately omitted. It only accepts 2 or 3,
    // and passing 2 selects Encore's plain "vue2" target, which still demands
    // vue-template-compiler. Left unset, Encore detects vue@2.7 from
    // package.json and picks its "vue2.7" target instead, which relies on the
    // SFC compiler that Vue 2.7 ships itself.
    // `runtimeCompilerBuild` is required because the Twig templates mount
    // components against in-DOM templates rather than render functions.
    .enableVueLoader(() => {}, { runtimeCompilerBuild: true })
    // Encore sets ts-loader's `appendTsSuffixTo` to /\.vue$/ by itself once the
    // Vue loader is enabled, so no configuration callback is needed here.
    .enableTypeScriptLoader()
    .enableSassLoader(options => {
        options.sassOptions = {
            silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'legacy-js-api'],
            quietDeps: true,
        };
    })
    .enablePostCssLoader();

module.exports = Encore.getWebpackConfig();
