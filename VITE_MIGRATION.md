# Webpack to Vite Migration

This project has been migrated from Webpack to Vite for improved build performance and developer experience.

## What Changed

### Build System
- **Webpack** has been replaced with **Vite 7.1.2**
- **Build commands** updated:
  - `npm run watch` → `npm run watch` (watch mode) or `npm run dev` (dev server)
  - `npm run build:dev` → `npm run build:dev` (same)
  - `npm run build` → `npm run build` (same)

### Configuration
- `webpack.config.js` → `vite.config.mjs`
- All entry points preserved from original webpack configuration
- External dependencies (jQuery, TinyMCE) handled the same way
- Manifest generation for cache-busting maintained

### Dependencies
- Removed all Webpack-related packages
- Added Vite, modern Sass, TypeScript 5.9
- Babel only used for Jest testing
- PostCSS with autoprefixer for CSS processing

## What Still Works

- ✅ All JavaScript and TypeScript files
- ✅ Icon generation (material-symbols → assets/generated/icon.json)
- ✅ Raw file imports (`!!raw-loader!file?raw` syntax)
- ✅ External dependencies
- ✅ Jest tests
- ✅ Cache-busting manifest.json
- ✅ Development and production builds

## Known Issues

- SCSS files that import `@helsingborg-stad/styleguide` will need that package available
- When the styleguide package is available, update the vite.config.mjs to remove it from externals

## Benefits

- **Faster builds** - Vite uses esbuild for faster transpilation
- **Faster development** - Hot module replacement and dev server
- **Simpler configuration** - Less complex than Webpack setup
- **Better ES modules** - Native ESM support
- **Smaller bundle** - Tree-shaking and optimization

## For Developers

No changes needed for most development workflows. The same npm scripts work as before. Asset output structure is identical to maintain compatibility with deployment scripts.