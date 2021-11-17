Bolt Cypress end-to-end tests
===
  Bolt uses [Cypress](https://docs.cypress.io/guides/overview/why-cypress) for E2E testing.
  
Installation
---
  Make sure you have set up a Bolt development environment. You can find a guide for that [here](https://github.com/bolt/core/tree/master#progress).
  
  Cypress has the [Electron](https://www.electronjs.org) browser built-in and uses it by default, but you can change which browser it operates in by following [this guide](https://docs.cypress.io/guides/guides/launching-browsers) from the official documentation.
  
Usage
---
  The Cypress test use a specific port on localhost. We configured the port to be 8088 so if you would like to run these tests you can run the following commands:
  
  To start a server on this port you can use this command inside your project folder:
  ```
  php -S localhost:8088
  ```

  To run all tests use:
  ```
  npm run cypress:dev
  ```
  
  To run a specific test use:
  ```
  npm run cypress:dev -- --spec "./tests/cypress/integration/your_test.spec.js"
  ```
  
  If you want to run the test on the same project make sure to run `make db-reset` otherwise it will fail on a few tests that depend on the standard fixtures.
  
  You can add additional options to the run command by typing `--` and the option you want after it. A list of all options is available [here](https://docs.cypress.io/guides/guides/command-line#Commands).
    
Writing / editing tests
---
### Writing
If you think that there is a test missing or you have created a new feature which needs to be included with the cypress tests. To create a test you can follow these instructions:
   - Put your tests in the `./tests/cypress/integration/` folder.
     
   - Cypress has its own syntax. A list of all built-in commands and how to use them can be found [here](https://docs.cypress.io/api/table-of-contents).</li>
  
   - Additional commands are available through the [@testing-library/cypress](https://github.com/testing-library/cypress-testing-library).</li>
  
   - You can add additional commands for testing by following the tutorial in [this git repository](https://github.com/testing-library/cypress-testing-library).
  
   - For writing custom commands, refer to the [Cypress documentation](docs.cypress.io/api/cypress-api/custom-commands)</li>

### Editing
If you made a pull request and the Cypress tests keep failing you might need to edit the Cypress tests. Before you start editing these go through these points
**When does a test need editing:**
*The Cypress tests fail on your pull request* 
  - Does the test fail **once or twice** there is nothing broken it will most likely be fixed after a retrigger (or multiple if you're lucky 😁).
  - If 3 out of 3 tests fail then check what is failing and try to retrigger the tests as well (2 - 3 times). If it keeps failing on the same test you need           to edit the tests which are failing.

To edit a test you need to make sure you will still be testing the functionality of the test and not find a simple workaround.

So try to fix the test within your custom written code. If that is not possible then you need to edit the test.
