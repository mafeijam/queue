const mix = require('laravel-mix')
const path = require('path')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')

mix.js('resources/js/app.js', 'public/js')
  .postCss('resources/css/app.css', 'public/css')
  .vue()
  .version()

mix.webpackConfig({
  output: {
    chunkFilename: 'js/[name].js?id=[chunkhash]',
  },
  resolve: {
    alias: {
      '@': path.resolve('resources/js'),
    },
  },
  module: {
    rules: [{
      test: /\.pug$/,
      loader: 'pug-plain-loader'
    }]
  },
  plugins: [
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ['js/*', 'css/*'],
      verbose: true
    })
  ],
})
