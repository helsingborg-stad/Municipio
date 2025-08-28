import { defineConfig } from 'vite'
import babel from 'vite-plugin-babel'
import path from 'path'
import fs from 'fs'
import copy from 'rollup-plugin-copy'
const { manifestPlugin } = await import('vite-plugin-simple-manifest').then(m => m.default || m)

const entries = {
  'css/splide': './node_modules/@splidejs/splide/dist/css/splide-core.min.css',
  'css/styleguide': './node_modules/@helsingborg-stad/styleguide/source/sass/main.scss',
  'js/styleguide': './node_modules/@helsingborg-stad/styleguide/source/js/app.js',

  'css/municipio': './assets/source/sass/main.scss',
  'css/mce': './assets/source/sass/mce.scss',
  'css/blockeditor': './assets/source/sass/blockeditor.scss', // depends on styleguide
  'css/acf': './assets/source/sass/admin/acf.scss',
  'css/header-flexible': './assets/source/sass/admin/header-flexible.scss',
  'css/general': './assets/source/sass/admin/general.scss',
  'css/a11y': './assets/source/sass/admin/a11y.scss',
  'css/login': './assets/source/sass/admin/login.scss',
  'css/trash-page': './assets/source/sass/admin/trash-page.scss',

  'fonts/material/light/sharp': './assets/source/sass/icons/light/sharp.scss',
  'fonts/material/light/outlined': './assets/source/sass/icons/light/outlined.scss',
  'fonts/material/light/rounded': './assets/source/sass/icons/light/rounded.scss',
  'fonts/material/medium/sharp': './assets/source/sass/icons/medium/sharp.scss',
  'fonts/material/medium/outlined': './assets/source/sass/icons/medium/outlined.scss',
  'fonts/material/medium/rounded': './assets/source/sass/icons/medium/rounded.scss',
  'fonts/material/bold/sharp': './assets/source/sass/icons/bold/sharp.scss',
  'fonts/material/bold/outlined': './assets/source/sass/icons/bold/outlined.scss',
  'fonts/material/bold/rounded': './assets/source/sass/icons/bold/rounded.scss',

  'js/municipio': './assets/source/js/municipio.js',
  'js/instantpage': './node_modules/instant.page/instantpage.js',
  'js/mce-buttons': './assets/source/mce-js/mce-buttons.js',
  'js/mce-table': './assets/source/mce-js/mce-table.js',
  'js/pdf': './assets/source/js/pdf.ts',
  'js/nav': './assets/source/js/nav.ts',

  /* Admin js */
  'js/color-picker': './assets/source/js/admin/colorPicker.js',
  'js/design-share': './assets/source/js/admin/designShare.ts',
  'js/customizer-preview': './assets/source/js/admin/customizerPreview.js',
  'js/customizer-flexible-header': './assets/source/js/admin/customizerFlexibleHeader.ts',
  'js/hidden-post-status-conditional': './assets/source/js/admin/acf/hiddenPostStatusConditional.ts',
  'js/user-group-visibility': './assets/source/js/admin/private/userGroupVisibility.ts',
  'js/widgets-area-hider': './assets/source/js/admin/widgetsAreaHider.js',
  'js/customizer-error-handling': './assets/source/js/admin/customizerErrorHandling.ts',
  'js/blocks/columns': './assets/source/js/admin/blocks/columns.js',
  'js/event-source-progress': './assets/source/js/admin/eventSourceProgress/index.ts'
}

function convertIconManifestPlugin() {
  return {
    name: 'icon-generator',
    buildStart() {
      try {
        const rawData = readData();
        const validData = validateData(rawData);
        const transformedData = transformData(validData);
        writeData(transformedData);
        console.log('Generated icon.json successfully');
      } catch (err) {
        if (err.code !== 'ENOENT') console.error('Error generating icons:', err);
      }
    }
  }

  function readData() {
    const sourcePath = path.resolve(process.cwd(), 'node_modules/material-symbols/index.d.ts');
    return fs.readFileSync(sourcePath, 'utf8');
  }

  function validateData(data) {
    // Match only the first array assigned to a variable (typical in d.ts files)
    const match = data.match(/=\s*(\[[^\]]*\])/s);
    if (!match) throw new Error('Could not parse icon data. Source file malformed.');

    try {
      return JSON.parse(match[1]); // Use only the captured array
    } catch (err) {
      throw new Error(`Error parsing icon data: ${err.message}`);
    }
  }

  function transformData(icons) {
    // Example: we could filter, sort, or restructure icons here
    return icons.sort(); // simple alphabetical sort
  }

  function writeData(icons) {
    const outputDir = path.resolve(process.cwd(), 'assets/generated');
    fs.mkdirSync(outputDir, { recursive: true });
    const outputPath = path.resolve(outputDir, 'icon.json');
    fs.writeFileSync(outputPath, JSON.stringify(icons, null, 2));
  }
}

export default defineConfig(({ mode }) => {
  const isProduction = mode === 'production'
  return {
    build: {
      outDir: 'assets/dist',
      emptyOutDir: true,
      lib: false, // Disable lib mode to avoid ES modules
      rollupOptions: {
        input: entries,
        external: ['jquery', 'tinymce'],
        output: {
          manualChunks: (id, { getModuleInfo, getModuleIds }) => {
            // Force all modules to be inlined - don't create shared chunks
            return null;
          },
          globals: {
            jquery: 'jQuery',
            tinymce: 'tinymce'
          },
          entryFileNames: isProduction ? '[name].[hash].js' : '[name].js',
          chunkFileNames: isProduction ? '[name].[hash].js' : '[name].js',
          assetFileNames: (assetInfo) => {
            if (assetInfo.name?.endsWith('.css')) {
              return isProduction ? '[name].[hash].css' : '[name].css'
            }
            // Handle font files with custom naming for material symbols
            if (assetInfo.name?.match(/\.(woff2?|ttf|eot|svg|otf)$/)) {
              const name = assetInfo.name;
              if (name.includes('material-symbols')) {
                // Extract weight information from source path context  
                const source = assetInfo.source || '';

                // Extract weight and style from filename
                let weight = 'medium'; // default
                let style = 'sharp'; // default

                if (name.includes('-200') || this.facadeModuleId?.includes('font-200')) {
                  weight = 'light';
                } else if (name.includes('-400') || this.facadeModuleId?.includes('font-400')) {
                  weight = 'medium';
                } else if (name.includes('-600') || this.facadeModuleId?.includes('font-600')) {
                  weight = 'bold';
                }

                if (name.includes('outlined')) {
                  style = 'outlined';
                } else if (name.includes('rounded')) {
                  style = 'rounded';
                }

                const ext = name.split('.').pop();
                return `fonts/material/${weight}/${style}.[hash].${ext}`;
              }
            }
            return 'assets/[name].[hash].[ext]'
          }
        },
        treeshake: {
          moduleSideEffects: false
        }
      },
      minify: isProduction ? 'esbuild' : false,
      sourcemap: true
    },
    // Ensure core-js is included for dependency optimization
    optimizeDeps: {
      include: ['core-js']
    },
    esbuild: {
      keepNames: true,
      minifyIdentifiers: false
    },
    css: {
      preprocessorOptions: {
        scss: {
          quietDeps: true, // Remove when issue is resolved: https://github.com/marella/material-symbols/issues/44
          api: 'modern-compiler',
          includePaths: ['node_modules', 'assets/source'],
          importers: [
            {
              findFileUrl(url) {
                if (url.startsWith('~')) {
                  return new URL(url.slice(1), new URL('../node_modules/', import.meta.url))
                }
                return null
              }
            }
          ]
        }
      }
    },
    resolve: {
      extensions: ['.tsx', '.ts', '.js', '.scss', '.css'],
      alias: {
        '~': path.resolve(process.cwd(), 'node_modules')
      },
      dedupe: ['leaflet']
    },
    plugins: [
      manifestPlugin('manifest.json'),
      convertIconManifestPlugin(),
      copy({
        targets: [
          {
            src: 'node_modules/@material-symbols/font-200/*.woff2',
            dest: 'assets/dist/fonts/material/light/'
          },
          {
            src: 'node_modules/@material-symbols/font-400/*.woff2',
            dest: 'assets/dist/fonts/material/medium/'
          },
          {
            src: 'node_modules/@material-symbols/font-600/*.woff2',
            dest: 'assets/dist/fonts/material/bold/'
          }
        ],
        hook: 'writeBundle'
      })
    ]
  }
})