module.exports = {
  $schema: "http://json.schemastore.org/eslintrc",
  globals: {
    route: "readonly",
  },
  env: {
    browser: true,
    es2021: true,
  },
  extends: [
    "eslint:recommended",
    "plugin:vue/vue3-essential",
    "plugin:prettier/recommended",
    "plugin:vue/recommended",
    "plugin:prettier-vue/recommended",
  ],
  parserOptions: {
    ecmaVersion: "latest",
    sourceType: "module",
  },
  plugins: ["vue"],
  rules: {
    "vue/multi-word-component-names": "off",
    "vue/max-attributes-per-line": "off",
    "max-len": "off",
    "import/prefer-default-export": "off",
    "vue/require-prop-types": "off",
    "prefer-destructuring": "off",
    "no-debugger": 0,
    "default-case": 0,
    "promise/always-return": 0,
    "comma-dangle": "only-multiline",
    quotes: [
      "warn",
      "single",
      {
        avoidEscape: true,
        allowTemplateLiterals: true,
      },
    ],
    "quote-props": ["error", "as-needed"],
  }
};
