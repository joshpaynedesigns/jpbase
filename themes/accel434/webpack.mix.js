let mix = require("laravel-mix");

mix.setPublicPath("dist");
mix.setResourceRoot("./");

mix.js("assets/js/sitewide.js", "dist/front.js");
mix.sass("assets/styles/style.scss", "dist/front.css");
mix.sass("assets/styles/editor-style.scss", "dist/editor-style.css");

mix.copy("assets/icons/src/*.svg", "dist/icons/");

mix.sourceMaps(true, "source-map");
