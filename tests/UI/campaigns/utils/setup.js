require('module-alias/register');

const helper = require('@utils/helpers');
const files = require('@utils/files');

let screenshotNumber = 1;

/**
 * Create unique browser for all mocha run
 */
before(async function () {
  this.browser = await helper.createBrowser();

  // Create object for browser errors
  if (global.BROWSER.interceptErrors) {
    global.browserErrors = {
      responses: [],
      js: [],
      console: [],
    };
  }
});

/**
 * Close browser after finish the run
 */
after(async function () {
  await helper.closeBrowser(this.browser);

  if (global.BROWSER.interceptErrors) {
    // Delete duplicated errors and create json report
    const browserErrors = {
      responses: [...new Set(global.browserErrors.responses)],
      js: [...new Set(global.browserErrors.js)],
      console: [...new Set(global.browserErrors.console)],
    };

    const reportName = await files.generateReportFilename();
    await files.createFile('.', `${reportName}.json`, JSON.stringify(browserErrors));
  }
});


afterEach(async function () {
  // Take screenshot if demanded after failed step
  if (global.TAKE_SCREESHOT_AFTER_FAIL && this.currentTest.state === 'failed') {
    const currentTab = await helper.getLastOpenedTab(this.browser);

    // Take a screenshot
    await currentTab.screenshot(
      {
        path: `./screenshots/fail_test_${screenshotNumber}.png`,
        fullPage: true,
      },
    );

    screenshotNumber += 1;
  }
});
