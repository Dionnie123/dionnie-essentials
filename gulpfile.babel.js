import yargs from "yargs";
import cleanCss from "gulp-clean-css";
import gulpif from "gulp-if";
import postcss from "gulp-postcss";
import sourcemaps from "gulp-sourcemaps";
import autoprefixer from "autoprefixer";
import { src, dest, watch, series, parallel } from "gulp";
import imagemin from "gulp-imagemin";
import webpack from "webpack-stream";
import named from "vinyl-named";
import del from "del";
import browserSync from "browser-sync";
import zip from "gulp-zip";
import info from "./package.json";
import replace from "gulp-replace";
import wpPot from "gulp-wp-pot";
import rename from "gulp-rename";

const PRODUCTION = yargs.argv.prod;

var sass = require("gulp-sass")(require("sass"));

var paths = {
  root: "./src",
  html: {
    src: "src/*.html",
  },
  styles: {
    src: "src/scss/**/*.scss",
    admin: "src/scss/**/*.scss",
    dest: "src/css",
  },
  scripts: {
    src: "src/js/**/*.js",
    dest: "src/js",
  },
};

export const styles = () => {
  return src([paths.styles.src, paths.styles.admin])
    .pipe(gulpif(!PRODUCTION, sourcemaps.init()))
    .pipe(sass().on("error", sass.logError))
    .pipe(gulpif(PRODUCTION, postcss([autoprefixer])))
    .pipe(gulpif(PRODUCTION, cleanCss({ compatibility: "ie8" })))
    .pipe(gulpif(!PRODUCTION, sourcemaps.write()))
    .pipe(dest(paths.styles.dest))
    .pipe(server.stream());
};

export const scripts = () => {
  return src([
    "src/js/bundle.js",
    "src/js/admin.js",
    "src/js/customize-preview.js",
  ])
    .pipe(named())
    .pipe(
      webpack({
        module: {
          rules: [
            {
              test: /\.js$/,
              use: {
                loader: "babel-loader",
                options: {
                  presets: ["@babel/preset-env"], //or ['babel-preset-env']
                },
              },
            },
          ],
        },
        mode: PRODUCTION ? "production" : "development",
        devtool: !PRODUCTION ? "inline-source-map" : false,
        output: {
          filename: "[name].js",
        },
        externals: {
          jquery: "jQuery",
        },
      })
    )
    .pipe(dest("dist/js"));
};

export const images = () => {
  return src("src/images/**/*.{jpg,jpeg,png,svg,gif}")
    .pipe(gulpif(PRODUCTION, imagemin()))
    .pipe(dest("dist/images"));
};

export const copy = () => {
  return src([
    "src/**/*",
    "!src/{images,js,scss}",
    "!src/{images,js,scss}/**/*",
  ]).pipe(dest("dist"));
};

export const watchForChanges = () => {
  watch("src/scss/**/*.scss", styles, reload);
  watch("src/images/**/*.{jpg,jpeg,png,svg,gif}", series(images, reload));
  watch(
    ["src/**/*", "!src/{images,js,scss}", "!src/{images,js,scss}/**/*"],
    series(copy, reload)
  );
  watch("src/js/**/*.js", series(scripts, reload));
  watch("**/*.php", reload);
};

export const clean = () => {
  return del(["dist", "bundled", "languages"]);
};

export const compress = () => {
  return src([
    "**/*",
    "!node_modules{,/**}",
    "!bundled{,/**}",
    "!src{,/**}",
    "!.babelrc",
    "!.gitignore",
    "!gulpfile.babel.js",
    "!package.json",
    "!package-lock.json",
  ])
    .pipe(replace("_plugintitle", info.title))
    .pipe(replace("_plugindescription", info.description))
    .pipe(replace("_pluginauthor", info.author))
    .pipe(replace("_pluginname_", info.name + "_"))
    .pipe(replace("_pluginname-", info.slug + "-"))
    .pipe(replace("_pluginname", info.slug))
    .pipe(replace("_PluginName", info.namespace))
    .pipe(
      rename(function (path) {
        console.log(path);
        if (path.basename.includes("_pluginname")) {
          path.basename = `${info.slug}`;
        }
      })
    )

    .pipe(
      rename(function (path) {
        path.dirname = `${info.slug}/` + path.dirname; // Change 'folder_name' to your desired folder name
      })
    )
    .pipe(dest("temp"))

    .pipe(zip(`${info.slug}.zip`))
    .pipe(dest("bundled"));
};

export const pot = () => {
  return src("**/*.php")
    .pipe(
      wpPot({
        domain: "_pluginname",
        package: info.name,
      })
    )
    .pipe(dest(`languages/${info.name}.pot`));
};

const server = browserSync.create();
export const serve = (done) => {
  server.init({
    proxy: "http://provider.test/",
  });
  done();
};
export const reload = (done) => {
  server.reload();
  done();
};

export const dev = series(
  clean,
  parallel(styles, images, copy, scripts),
  serve,
  watchForChanges
);
export const build = series(
  clean,
  parallel(styles, images, copy, scripts),
  pot,

  compress
);
export default dev;
