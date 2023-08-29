let mix = require("laravel-mix");

mix.setPublicPath("dist");
mix.setResourceRoot("./");

mix
  .js("assets/js/sitewide.js", "dist/front.js")
  .sass("assets/styles/style.scss", "dist/front.css")
  .sass("assets/styles/editor-style.scss", "dist/editor-style.css")
  .sourceMaps(true, "source-map");
