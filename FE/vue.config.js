var path = require('path');
module.exports = {
  chainWebpack: (config) => {
    config.module.rule('vue').use('vue-svg-inline-loader').loader('vue-svg-inline-loader').options({
      /* ... */
    });
  },
  configureWebpack: {
    resolve: {
      alias: {
        LibTest: path.resolve('../common')
      },
      modules: [path.resolve('../common')]
    },
    resolveLoader: {
      modules: [path.resolve('../common')]
    },
    output: {
      globalObject: 'this'
    }
  }
};
