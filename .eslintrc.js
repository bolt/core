module.exports = {
  parserOptions: {
    parser: "babel-eslint",
    ecmaVersion: 2017,
    sourceType: "module"
  },
  env: {
    "browser": true,
    "node": true
  },
  extends: [
    "eslint:recommended",
    "plugin:vue/recommended",
    "plugin:prettier/recommended",
    "prettier",
    "prettier/vue",
  ],
  rules: {
    "no-console": ["error", {allow: ["error", "warn"]}],
    "no-debugger": "error" ,
    "vue/require-default-prop": "off",
    "vue/require-prop-type-constructor": "off",
    "prettier/prettier": ["error", { printWidth: 120 }],
  },
  "plugins": [
    "standard",
    "vue",
    "prettier",
  ],
  settings: {
    'import/resolver': {
      node: {
        "extensions": [".js", ".vue"]
      }
    }
  }
};
