import { readFileSync, writeFileSync } from 'node:fs';

const icons = JSON.parse(readFileSync(new URL('../vendor/helsingborg-stad/material-design-icons-json-svg-font-reduced/symbols.json', import.meta.url)));

// This is intentionally a build-time glossary. The generated PHP file contains
// only literal strings so WordPress can discover them with make-pot.
const glossary = {
  accessibility: 'tillgänglighet', account: 'konto', add: 'lägg till', alarm: 'larm',
  alert: 'varning', all: 'alla', ambulance: 'ambulans', anchor: 'ankare',
  announcement: 'meddelande', apartment: 'lägenhet', app: 'app', archive: 'arkiv',
  arrow: 'pil', arrows: 'pilar', article: 'artikel', audio: 'ljud', back: 'bakåt',
  badge: 'märke', bank: 'bank', basket: 'korg', battery: 'batteri', beach: 'strand',
  bed: 'säng', bell: 'klocka', bike: 'cykel', bluetooth: 'bluetooth', book: 'bok',
  bookmark: 'bokmärke', bottle: 'flaska', box: 'ruta', calendar: 'kalender',
  call: 'samtal', camera: 'kamera', cancel: 'avbryt', car: 'bil', card: 'kort',
  cart: 'varukorg', castle: 'slott', chat: 'chatt', check: 'bock', child: 'barn',
  circle: 'cirkel', city: 'stad', clock: 'klocka', close: 'stäng', cloud: 'moln',
  code: 'kod', coffee: 'kaffe', comment: 'kommentar', computer: 'dator',
  copy: 'kopiera', crown: 'krona', cursor: 'pekare', dashboard: 'instrumentpanel',
  date: 'datum', delete: 'ta bort', delivery: 'leverans', description: 'beskrivning',
  device: 'enhet', directions: 'vägbeskrivning', download: 'ladda ner',
  edit: 'redigera', email: 'e-post', error: 'fel', event: 'evenemang',
  expand: 'expandera', export: 'exportera', eye: 'öga', face: 'ansikte',
  file: 'fil', filter: 'filter', fire: 'eld', flag: 'flagga', folder: 'mapp',
  food: 'mat', forward: 'framåt', fullscreen: 'helskärm', game: 'spel',
  gift: 'gåva', globe: 'jordglob', group: 'grupp', guide: 'guide',
  hand: 'hand', heart: 'hjärta', help: 'hjälp', history: 'historik',
  home: 'hem', hospital: 'sjukhus', house: 'hus', image: 'bild', import: 'importera',
  info: 'information', key: 'nyckel', keyboard: 'tangentbord', label: 'etikett',
  language: 'språk', left: 'vänster', light: 'ljus', link: 'länk', list: 'lista',
  location: 'plats', lock: 'lås', mail: 'post', map: 'karta', menu: 'meny',
  message: 'meddelande', mic: 'mikrofon', minus: 'minus', money: 'pengar',
  moon: 'måne', more: 'mer', mouse: 'mus', movie: 'film', music: 'musik',
  notification: 'notis', open: 'öppna', paint: 'måla', pause: 'paus',
  payment: 'betalning', people: 'personer', person: 'person', phone: 'telefon',
  photo: 'foto', pin: 'nål', place: 'plats', play: 'spela', plus: 'plus',
  print: 'skriv ut', public: 'offentlig', question: 'fråga', radio: 'radio',
  refresh: 'uppdatera', remove: 'ta bort', reply: 'svara', report: 'rapport',
  restaurant: 'restaurang', right: 'höger', room: 'rum', save: 'spara',
  search: 'sök', security: 'säkerhet', send: 'skicka', settings: 'inställningar',
  share: 'dela', shield: 'sköld', shop: 'butik', shopping: 'shopping',
  sign: 'skylt', star: 'stjärna', stop: 'stopp', store: 'butik',
  sun: 'sol', support: 'support', swap: 'byt', sync: 'synkronisera',
  table: 'tabell', tag: 'etikett', task: 'uppgift', text: 'text',
  thumb: 'tumme', time: 'tid', timer: 'timer', today: 'idag',
  train: 'tåg', trash: 'papperskorg', travel: 'resa', tree: 'träd',
  truck: 'lastbil', tv: 'tv', undo: 'ångra', unlock: 'lås upp',
  update: 'uppdatera', upload: 'ladda upp', user: 'användare',
  video: 'video', view: 'vy', visibility: 'synlighet', volume: 'volym',
  warning: 'varning', water: 'vatten', weather: 'väder', wifi: 'wifi',
  window: 'fönster', work: 'arbete', world: 'värld', zoom: 'zoom',
  up: 'upp', down: 'ner', off: 'av', on: 'på', new: 'ny', filled: 'fylld',
  outline: 'kontur', outlined: 'kontur', disabled: 'inaktiverad', active: 'aktiv',
};

const swedishLabelFor = (icon) => {
  const words = icon.split('_').map((word) => glossary[word] ?? word.replace(/\b\w/g, (letter) => letter.toUpperCase()));
  return words.join(' ');
};

const englishLabelFor = (icon) => icon
  .replaceAll('_', ' ')
  .replace(/\b\w/g, (letter) => letter.toUpperCase());

const escapePhp = (value) => value.replaceAll("'", "\\\\'");
const entries = icons.map((icon) => `            '${icon}' => __('${escapePhp(englishLabelFor(icon))}', 'municipio'),`).join('\n');
const output = `<?php\n\nnamespace Municipio\\Theme;\n\n/**\n * Generated, literal icon labels for WordPress translation extraction.\n */\nclass IconLabels\n{\n    /**\n     * @return array<string, string>\n     */\n    public static function get(): array\n    {\n        return [\n${entries}\n        ];\n    }\n}\n`;

writeFileSync(new URL('../library/Theme/IconLabels.php', import.meta.url), output);

const translations = Object.fromEntries(icons.map((icon) => [
  englishLabelFor(icon),
  swedishLabelFor(icon),
]));
writeFileSync(
  new URL('../languages/generated-icon-labels-sv.json', import.meta.url),
  `${JSON.stringify(translations, null, 2)}\n`,
);
