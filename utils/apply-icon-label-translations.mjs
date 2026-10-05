import { readFileSync, writeFileSync } from 'node:fs';

const poPath = new URL('../languages/sv_SE.po', import.meta.url);
const translations = JSON.parse(readFileSync(new URL('../languages/generated-icon-labels-sv.json', import.meta.url)));
const escapePo = (value) => value.replaceAll('\\', '\\\\').replaceAll('"', '\\"');

let po = readFileSync(poPath, 'utf8');
let applied = 0;

for (const [source, translation] of Object.entries(translations)) {
  const escapedSource = escapePo(source).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const expression = new RegExp(`(msgid "${escapedSource}"\\nmsgstr )""`, 'g');
  po = po.replace(expression, `$1"${escapePo(translation)}"`);
  applied += Number(expression.test(po));
}

writeFileSync(poPath, po);
console.log(`Applied Swedish draft translations for ${Object.keys(translations).length} icon labels.`);
