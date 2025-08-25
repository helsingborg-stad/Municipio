// Simple raw loader replacement for Jest
const fs = require('fs');

module.exports = {
  process(src, filename) {
    // Extract the actual file path from the import
    const match = filename.match(/!!raw-loader!(.+)$/);
    if (match) {
      const filePath = match[1];
      try {
        const content = fs.readFileSync(filePath, 'utf8');
        return {
          code: `module.exports = ${JSON.stringify(content)};`
        };
      } catch (err) {
        console.warn(`Could not load raw file: ${filePath}`, err);
        return {
          code: 'module.exports = "";'
        };
      }
    }
    return {
      code: `module.exports = ${JSON.stringify(src)};`
    };
  },
};