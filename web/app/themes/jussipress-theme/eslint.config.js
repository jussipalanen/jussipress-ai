import js from '@eslint/js'
import globals from 'globals'
import prettier from 'eslint-config-prettier'

export default [
  js.configs.recommended,

  {
    files: ['resources/js/**/*.{js,mjs}'],

    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        // Browser environment
        ...globals.browser,
        // WordPress globals injected at runtime
        wp: 'readonly',
        wpApiSettings: 'readonly',
        jQuery: 'readonly',
        $: 'readonly',
      },
    },

    rules: {
      // Correctness
      'no-unused-vars': ['error', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
      'no-undef': 'error',

      // Modern JS
      'no-var': 'error',
      'prefer-const': 'error',
      'prefer-arrow-callback': 'error',
      'object-shorthand': 'error',

      // Style
      'no-console': 'warn',
      eqeqeq: ['error', 'always', { null: 'ignore' }],
    },
  },

  // WordPress block editor entry — Node-like globals (process, module)
  {
    files: ['resources/js/editor.js'],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
        wp: 'readonly',
      },
    },
  },

  // Disable ESLint formatting rules — Prettier handles those
  prettier,
]
