Bolt Cypress end-to-end tests
===

Bolt uses [Cypress][cypress] for E2E testing.

Installation
---

Make sure you have set up a Bolt development environment. You can find a guide for that [here][dev].

Set up Cypress for initial usage, by running `./node_modules/.bin/cypress install`. 

Cypress has the [Electron][electron] browser built-in and uses it by default, but you can change 
which browser it operates in by following [this guide][guide] from the official documentation.

Usage
---
The Cypress test use a specific port on localhost. We configured the port to be 8088 so if you would 
like to run these tests you can run the following commands:

To start a server on this port you can use this command inside your project folder:
```
php -S localhost:8088 -t public
```

Or, if you're using the Symfony CLI: 

```
symfony serve -d --port=8088
```

### To run all tests use:
```
npm run cypress:dev
```
This one will run all the tests that are located in tests/cypress/integration. Once this is finished 
it will show a table on the command line which looks like this:

![Screenshot 2022-04-25 at 13 49 42](https://user-images.githubusercontent.com/40595903/165083309-004f8c80-0d15-4400-9107-876d4c07e615.png)

This shows all the tests that have been run and gives an overview of which failed and which 
succeeded. At the bottom you have a summary of how many tests failed.


### To run a specific test use:

```
npm run cypress:dev -- --spec "./tests/cypress/integration/your_test.spec.js"
```

If you want to run the test on the same project make sure to run `make db-reset-without-images`, 
because otherwise it might fail on a few tests that depend on the standard fixtures.

You can add additional options to the run command by typing `--` and the option you want after it. 
A list of all options is available [here][options].

If you want to see what's happening, as the test is running, use the `--headed` option. For example:

```
npm run cypress:dev -- --headed --spec "./tests/cypress/integration/homepage.spec.js"
```

Evidence
---
Once you've run any of these commands you can take a look at some 'evidence' cypress makes while 
running the tests. These are videos and screenshots of what it did during the test. These videos can 
be really informing about what failed and why. These are written to the filesystem at `./tests/cypress/evidence/`

#### Here you have an example of a screenshot of a failed test:

![As an Admin I am able to use the files section -- checks if an admin can cancel deleting files in the Files section (failed)](https://user-images.githubusercontent.com/40595903/165084073-c2becad6-b2db-4c48-bfce-e5f14e18ce05.png)

#### And here is an example of an video of a test:

https://user-images.githubusercontent.com/40595903/165084182-b749f781-266f-46b7-a821-1e145991bbc2.mp4

 #### All of these images and videos are available in `tests/cypress/evidence`. 

The tests that are run via **GitHub** also provide these videos and screenshots, but you can find 
them by going to the test tab on a pull request:

<img width="1431" alt="Screenshot 2022-04-25 at 13 58 10" src="https://user-images.githubusercontent.com/40595903/165084804-c350f108-9682-4eeb-af22-9ace48d92a68.png">

After going there you need to click on `> Cypress tests`:

<img width="1439" alt="Screenshot 2022-04-25 at 14 00 16" src="https://user-images.githubusercontent.com/40595903/165085204-a86cd0ef-82ac-4895-9d23-9beef208d3b0.png">

And then (this only shows up when the test has failed) scroll down to Artifacts and download 
`cypress-evidence`:
 
<img width="1055" alt="Screenshot 2022-04-25 at 14 03 36" src="https://user-images.githubusercontent.com/40595903/165085478-fd176fc1-fb68-481c-90fe-2490ad660331.png">


Writing / editing tests
---

### Writing

If you think that there is a test missing or you have created a new feature which needs to be 
included with the cypress tests. To create a test you can follow these instructions:

  - Put your tests in the `./tests/cypress/integration/` folder. 
  - Cypress has its own syntax. A list of all built-in commands and how to use them can be found [here](https://docs.cypress.io/api/table-of-contents)
  - Additional commands are available through the [@testing-library/cypress](https://github.com/testing-library/cypress-testing-library)
  - You can add additional commands for testing by following the tutorial in [this git repository](https://github.com/testing-library/cypress-testing-library)
  - For writing custom commands, refer to the [Cypress documentation](docs.cypress.io/api/cypress-api/custom-commands)

### Editing

If you made a pull request and the Cypress tests keep failing you might need to edit the Cypress tests. Before you start 
editing these go through these points
**When does a test need editing:**
*The Cypress tests fail on your pull request*

  - Does the test fail **once or twice** there is nothing broken it will most likely be fixed after a retrigger (or 
    multiple if you're unlucky 😁).
  - If 3 out of 3 tests fail then check what is failing and try to retrigger the tests as well (2 - 3 times). If it 
    keeps failing on the same test you need to edit the tests which are failing.

To edit a test you need to make sure you will still be testing the functionality of the test and not find a simple workaround.

So try to fix the test within your custom written code. If that is not possible then you need to edit the test.

[cypress]: https://docs.cypress.io/guides/overview/why-cypress
[dev]: https://github.com/bolt/core/tree/master#progress
[electron]: https://www.electronjs.org
[guide]: https://docs.cypress.io/guides/guides/launching-browsers
[options]: https://docs.cypress.io/guides/guides/command-line#Commands
