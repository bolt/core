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
    "no-console": process.env.NODE_ENV === "production" ? "error" : "off",
    "no-debugger": process.env.NODE_ENV === "production" ? "error" : "off",
    "vue/require-prop-types": "off",
    "prettier/prettier": "error",
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