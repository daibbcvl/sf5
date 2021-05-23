var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/admin_build')
    .setPublicPath('/build/admin_build')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .enableVersioning()
    .addEntry('admin', './assets/admin/js/admin.js')
    .enableSingleRuntimeChunk()
    .addRule({
        test: require.resolve('./assets/admin/js/vendors.bundle.js'),
        use: ['script-loader']
    })
    .addRule({
        test: require.resolve('./assets/admin/js/scripts.bundle.js'),
        use: ['script-loader']
    })
    .copyFiles([
        {from: './node_modules/ckeditor/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor/skins', to: 'ckeditor/skins/[path][name].[ext]'}
    ])
;

// build the admin configuration
const adminConfig = Encore.getWebpackConfig();

// Set a unique name for the config (needed later!)
adminConfig.name = 'adminConfig';

// reset Encore to build the second config
// Encore.reset();
//
//
// Encore
//     .setOutputPath('public/build/app_build')
//     .setPublicPath('/build/app_build')
//     .addEntry('app', './assets/app/js/app.js')
//     .enableSingleRuntimeChunk()
//     .enableSourceMaps(!Encore.isProduction())
//     .copyFiles([
//         {from: './assets/app/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
//     ])
//
// ;
//
// // build the second configuration
// const appConfig = Encore.getWebpackConfig();
//
// appConfig.name = 'appConfig';

module.exports = [adminConfig];
