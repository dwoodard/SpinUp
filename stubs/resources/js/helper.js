export const toTitleCase = (str) => str.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());

export const toKebabCase = (str) => str.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase();

export const score = function (a, b) { if (this === a) return 1; if (a === '') return 0; let c = 0; const d = a.length; let e = this; const f = e.length; let g; let h; let i = 1; let j; for (let k = 0, l, m, n, o, p, q; k < d; ++k) { n = a.charAt(k), o = e.indexOf(n.toLowerCase()), p = e.indexOf(n.toUpperCase()), q = Math.min(o, p), m = q > -1 ? q : Math.max(o, p); if (m === -1) { if (b) { i += 1 - b; continue; } return 0; }l = 0.1, e[m] === n && (l += 0.1), m === 0 ? (l += 0.6, k === 0 && (g = 1)) : e.charAt(m - 1) === ' ' && (l += 0.8), e = e.substring(m + 1, f), c += l; }h = c / d, j = (h * (d / f) + h) / 2, j /= i, g && j + 0.15 < 1 && (j += 0.15); return j; };

export const toPascalCase = (str) => {
  return str.replace(/[-_\s]+/g, ' ').split(' ').map((word) => word[0].toUpperCase() + word.slice(1)).join('');
};

export const pascalToTitleCase = (str) => {
  return str.replace(/([A-Z])/g, ' $1').replace(/^\s/, '');
};

export const isValidJson = (str) => {
  try {
    JSON.parse(str);
  } catch (e) {
    return false;
  }
  return true;
};

// eslint-disable-next-line no-extend-native
String.prototype.toTitleCase = function () {
  return this.split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ');
};
