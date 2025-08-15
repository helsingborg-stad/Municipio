import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  build: {
    outDir: 'assets/dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        'js/fab': './assets/source/3.0/js/fab.js',
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: '[name].js',
        assetFileNames: '[name].[ext]'
      }
    },
    minify: false,
    sourcemap: true
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js', '.scss', '.css']
  }
})