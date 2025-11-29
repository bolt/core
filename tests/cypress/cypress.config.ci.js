const { defineConfig } = require('cypress');

module.exports = defineConfig({
    e2e: {
        baseUrl: 'http://127.0.0.1:8088',
        specPattern: 'tests/cypress/integration/**/*.spec.js',
        supportFile: 'tests/cypress/support/index.js',
        screenshotsFolder: 'tests/cypress/evidence/screenshots',
        video: true,
        videosFolder: 'tests/cypress/evidence/videos',
        defaultCommandTimeout: 8000,
        viewportWidth: 1920,
        viewportHeight: 1080,
        scrollBehavior: 'nearest',
    },
});
