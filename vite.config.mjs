import { defineConfig } from 'vite'
import path from 'path'
import fs from 'fs'
import copy from 'rollup-plugin-copy'
const { manifestPlugin } = await import('vite-plugin-simple-manifest').then(m => m.default || m)

const entries = {
  'css/styleguide': '', // will be filled by plugin
  'js/styleguide': '', // will be filled by plugin

  'css/municipio': './assets/source/sass/main.scss',
  'css/mce': './assets/source/sass/mce.scss',
  'css/blockeditor': './assets/source/sass/blockeditor.scss',
  'css/acf': './assets/source/sass/admin/acf.scss',
  'css/header-flexible': './assets/source/sass/admin/header-flexible.scss',
  'css/general': './assets/source/sass/admin/general.scss',
  'css/a11y': './assets/source/sass/admin/a11y.scss',
  'css/login': './assets/source/sass/admin/login.scss',

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

// Custom plugin to generate icon data (replaces webpack plugin)
function iconGeneratorPlugin() {
  return {
    name: 'icon-generator',
    buildStart() {
      const filePath = path.resolve(process.cwd(), 'node_modules', 'material-symbols', 'index.d.ts');
      
      try {
        const data = fs.readFileSync(filePath, 'utf8');
        
        const [startIndex, endIndex] = [
          data.indexOf('['),
          data.indexOf(']')
        ];

        if (startIndex === -1 || endIndex === -1) {
          console.error('Could not parse source file. Source file malformed.');
          return;
        }

        let iconArray = [];
        try {
          iconArray = JSON.parse(data.substring(startIndex, endIndex + 1));
        } catch (parseError) {
          console.error(`Error parsing icon data: ${parseError}`);
          return;
        }

        const json = JSON.stringify(iconArray, null, 2);
        const resultDirectory = path.resolve(process.cwd(), 'assets', 'generated');
        const resultFilepath = path.resolve(resultDirectory, 'icon.json');

        try {
          fs.mkdirSync(resultDirectory, { recursive: true });
          fs.writeFileSync(resultFilepath, json);
          console.log('Generated icon.json successfully');
        } catch (err) {
          console.error(err);
        }
      } catch (err) {
        if (err.code !== 'ENOENT') {
          console.error(`Error reading icon file: ${filePath} [${err}]`);
        }
      }
    }
  }
}

// Custom plugin to handle raw imports (replaces raw-loader)
function rawPlugin() {
  return {
    name: 'raw',
    resolveId(id, importer) {
      if (id.includes('!!raw-loader!') && id.endsWith('?raw')) {
        // Extract the actual file path from !!raw-loader!./file.css?raw
        const filePath = id.replace('!!raw-loader!', '').replace('?raw', '');
        
        // If it's a relative path, resolve it relative to the importer
        if (filePath.startsWith('./') && importer) {
          const resolvedPath = path.resolve(path.dirname(importer), filePath);
          return resolvedPath + '?raw';
        }
        
        return filePath + '?raw';
      }
      if (id.endsWith('?raw')) {
        return id;
      }
    },
    load(id) {
      if (id.endsWith('?raw')) {
        // Remove the ?raw suffix to get the actual file path
        const filePath = id.replace('?raw', '');
        
        try {
          const content = fs.readFileSync(filePath, 'utf8');
          return `export default ${JSON.stringify(content)};`;
        } catch (err) {
          console.warn(`Could not load raw file: ${filePath}`, err.message);
          return `export default "";`;
        }
      }
    }
  }
}

function styleguideEntryResolver(entries) {
  return {
    name: 'styleguide-entry-resolver',
    buildStart() {
      // Resolve JS
      const jsFiles = fs.readdirSync('node_modules/@helsingborg-stad/styleguide/dist/js');
      const jsFile = jsFiles.find(f => f.startsWith('styleguide-js') && f.endsWith('.js'));
      if (jsFile) {
        entries['js/styleguide'] = path.resolve('node_modules/@helsingborg-stad/styleguide/dist/js', jsFile);
      }

      // Resolve CSS
      const cssFiles = fs.readdirSync('node_modules/@helsingborg-stad/styleguide/dist/css');
      const cssFile = cssFiles.find(f => f.startsWith('styleguide-css') && f.endsWith('.css'));
      if (cssFile) {
        entries['css/styleguide'] = path.resolve('node_modules/@helsingborg-stad/styleguide/dist/css', cssFile);
      }
    }
  }
}

export default defineConfig(({ mode }) => {
  const isProduction = mode === 'production'
  return {
    build: {
      outDir: 'assets/dist',
      emptyOutDir: true,
      rollupOptions: {
        input: entries,
        external: ['jquery', 'tinymce'],
        output: {
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
                // Extract weight information from path
                const source = assetInfo.source || '';
                const weightMap = {
                  'font-200': 'light',
                  'font-400': 'medium', 
                  'font-600': 'bold',
                };
                
                // Try to determine weight from filename or use unknown
                let weight = 'unknown';
                for (const [key, value] of Object.entries(weightMap)) {
                  if (name.includes(key)) {
                    weight = value;
                    break;
                  }
                }
                
                const baseName = name.replace(/^material-symbols-/, '').replace(/\.[^.]+$/, '');
                const ext = name.split('.').pop();
                return `fonts/material/${weight}/${baseName}.[hash].${ext}`;
              }
            }
            return 'assets/[name].[hash].[ext]'
          }
        }
      },
      minify: isProduction ? 'esbuild' : false,
      sourcemap: true
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
      }
    },
    plugins: [
      manifestPlugin('manifest.json'),
      iconGeneratorPlugin(),
      rawPlugin(),
      styleguideEntryResolver(entries)
    ]
  }
})