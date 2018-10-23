var Encore = require('@symfony/webpack-encore');
require('babel-polyfill');

const WorkboxPlugin = require('workbox-webpack-plugin');

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('public/assets/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/assets')

    
    .addEntry('bolt', './assets/js/bolt.js')
    .createSharedEntry('vendor', ['babel-polyfill'])
    // .addEntry('markdown', './assets/js/markdown.js')

    .autoProvidejQuery()
    .enableVueLoader()
    .enableSassLoader()

    // TODO: To keep or be removed if not needed
    // filenames include a hash that changes whenever the file contents change
    // .enableVersioning()

    // Workbox should always be the last plugin to add @see: https://developers.google.com/web/tools/workbox/guides/codelabs/webpack#optional-config
    // .addPlugin(
    //    new WorkboxPlugin.GenerateSW({
    //        // these options encourage the ServiceWorkers to get in there fast
    //        // and not allow any straggling "old" SWs to hang around
    //        clientsClaim: true,
    //        skipWaiting: false,
    //        importsDirectory: 'sw/',
    // })) 
;

// export the final configuration
module.exports = Encore.getWebpackConfig();
