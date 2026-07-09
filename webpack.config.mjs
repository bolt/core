import WebpackBar from 'webpackbar';
import Encore from '@symfony/webpack-encore';
import HtmlWebpackPlugin from 'html-webpack-plugin';
import { CleanWebpackPlugin } from 'clean-webpack-plugin';
import webpack from 'webpack';

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

    .addEntry('bolt', './assets/js/bolt.ts')
    .addEntry('zxcvbn', './assets/js/zxcvbn.js')
    .addEntry('vibrant', './assets/js/vibrant.js')
    .addStyleEntry('theme-default', './assets/scss/themes/default.scss')
    .addStyleEntry('theme-light', './assets/scss/themes/light.scss')
    .addStyleEntry('theme-dark', './assets/scss/themes/dark.scss')
    .addStyleEntry('theme-woordpers', './assets/scss/themes/woordpers.scss')

    .splitEntryChunks()
    .autoProvidejQuery()
    .enableSassLoader((options) => {
        options.sassOptions = {
            silenceDeprecations: [
                'import',
                'global-builtin',
                'color-functions',
                'legacy-js-api',
            ],
            quietDeps: true,
        };
    })
    .enablePostCssLoader()
    .enableTypeScriptLoader((options) => {
        options.appendTsSuffixTo = [/\.vue$/];
        options.transpileOnly = true;
    })

    .enableVueLoader(() => {}, {
        version: 3,
        runtimeCompilerBuild: true,
    })

    .addPlugin(
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: false,
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
        }),
    );

export default Encore.getWebpackConfig();
