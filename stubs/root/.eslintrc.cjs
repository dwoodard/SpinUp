export default {
  $schema: 'http://json.schemastore.org/eslintrc',
  globals: {
    route: 'readonly',
  },
  env: {
    browser: true,
    es2021: true,
  },
  extends: ['eslint:recommended', 'plugin:vue/vue3-essential'],
  parser: 'vue-eslint-parser',

  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module',
  },
  plugins: ['vue'],
  rules: {
    'vue/multi-word-component-names': 'off',
    'vue/max-attributes-per-line': 'off',
    'max-len': 'off',
    'import/prefer-default-export': 'off',
    'vue/require-prop-types': 'off',
    'prefer-destructuring': 'off',
    'no-debugger': 0,
    'default-case': 0,
    'promise/always-return': 0,
    'comma-dangle': ['error', 'only-multiline'],
    quotes: [
      'warn',
      'single',
      {
        avoidEscape: true,
        allowTemplateLiterals: true,
      },
    ],
    'quote-props': ['error', 'as-needed'],
    'eol-last': ['error', 'always'],
    'linebreak-style': ['error', 'unix'],
    'max-lines': 'off',
    'no-trailing-spaces': ['error'],
    'unicode-bom': ['error', 'never'],
    'no-unused-vars': [
      'error',
      {
        vars: 'all',
        args: 'none',
        ignoreRestSiblings: true,
        caughtErrors: 'all',
      },
    ],
    'array-bracket-newline': [
      'error',
      {
        multiline: true,
      },
    ],
    'no-multiple-empty-lines': [
      'error',
      {
        max: 3,
        maxEOF: 1,
      },
    ],
  },
}
