module.exports = {
    root: true,
    env: {
        node: true,
    },
    parser: 'vue-eslint-parser',
    parserOptions: {
        parser: '@babel/eslint-parser',
        sourceType: 'module',
    },
    extends: [
        // add more generic rulesets here, such as:
        // 'eslint:recommended',
        'plugin:vue/vue3-recommended',
    ],
    rules: {
        // override/add rules settings here, such as:
        // 'vue/no-unused-vars': 'error'
    }
}