const mix = require('laravel-mix');
require('laravel-mix-clean');

mix
  .setPublicPath('dist')
  .js('src/js/main.js', 'scripts')
  .options({ processCssUrls: false });

if (mix.inProduction()) {
  mix.clean();
}

mix
  .sourceMaps()
  .version();

