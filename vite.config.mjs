import { defineConfig } from 'vite'
import { resolve } from 'path'
import fs from 'fs'
const { manifestPlugin } = await import('vite-plugin-simple-manifest').then(m => m.default || m)

// Entry points configuration matching the original webpack config
const entries = {
  'js/styleguide': './assets/source/3.0/js/styleguide.js',
  'js/municipio': './assets/source/3.0/js/municipio.js',
  'js/instantpage': './node_modules/instant.page/instantpage.js',
  'js/mce-buttons': './assets/source/3.0/mce-js/mce-buttons.js',
  'js/mce-table': './assets/source/3.0/mce-js/mce-table.js',
  'js/pdf': './assets/source/3.0/js/pdf.ts',
  'js/nav': './assets/source/3.0/js/nav.ts',

  /* Admin js */
  'js/color-picker': './assets/source/3.0/js/admin/colorPicker.js',
  'js/design-share': './assets/source/3.0/js/admin/designShare.ts',
  'js/customizer-preview': './assets/source/3.0/js/admin/customizerPreview.js',
  'js/customizer-flexible-header': './assets/source/3.0/js/admin/customizerFlexibleHeader.ts',
  'js/hidden-post-status-conditional': './assets/source/3.0/js/admin/acf/hiddenPostStatusConditional.ts',
  'js/user-group-visibility': './assets/source/3.0/js/admin/private/userGroupVisibility.ts',
  'js/widgets-area-hider': './assets/source/3.0/js/admin/widgetsAreaHider.js',
  'js/customizer-error-handling': './assets/source/3.0/js/admin/customizerErrorHandling.ts',
  'js/blocks/columns': './assets/source/3.0/js/admin/blocks/columns.js',
  'js/event-source-progress': './assets/source/3.0/js/admin/eventSourceProgress/index.ts',

  /* Legacy 2.0  */
  'js/mce-pricons': './assets/source/3.0/mce-js/mce-pricons.js',
  'js/mce-metadata': './assets/source/3.0/mce-js/mce-metadata.js',
}

// Custom plugin to generate icon data (replaces webpack plugin)
function iconGeneratorPlugin() {
  return {
    name: 'icon-generator',
    buildStart() {
      const filePath = resolve(process.cwd(), 'node_modules', 'material-symbols', 'index.d.ts');
      
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
        const resultDirectory = resolve(process.cwd(), 'assets', 'generated');
        const resultFilepath = resolve(resultDirectory, 'icon.json');

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
    load(id) {
      if (id.endsWith('?raw')) {
        const filePath = id.replace('?raw', '');
        const content = fs.readFileSync(filePath, 'utf8');
        return `export default ${JSON.stringify(content)};`;
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
        '~': resolve(process.cwd(), 'node_modules')
      }
    },
    plugins: [
      manifestPlugin('manifest.json'),
      iconGeneratorPlugin(),
      rawPlugin()
    ]
  }
})