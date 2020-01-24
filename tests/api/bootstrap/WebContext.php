<?php

declare(strict_types=1);

namespace Context;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\WebAssert;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines steps for web tests.
 * @todo: MinkContext should be extended just once. Take this out into a CommonContext.
 */
class WebContext extends MinkContext implements Context
{
    /**
     * @When I wait for :cssSelector
     * @param $cssSelector
     * @throws \Exception
     */
    public function iWaitFor($cssSelector)
    {
        $this->spin(function(WebContext $context) use ($cssSelector) {
            return !is_null($context->getSession()->getPage()->find('css', $cssSelector));
        });
    }

    /**
     * @When I wait for :text to appear
     * @Then I should see :text appear
     * @param $text
     * @throws \Exception
     */
    public function iWaitForTextToAppear($text)
    {
        $this->spin(function(WebContext $context) use ($text) {
            $context->assertPageContainsText($text);
            return true;
        });
    }

    /**
     * @When I wait for :text to disappear
     * @Then I should see :text disappear
     * @param $text
     * @throws \Exception
     */
    public function iWaitForTextToDisappear($text)
    {
        $this->spin(function(WebContext $context) use ($text) {
            $context->assertPageNotContainsText($text);
            return true;
        });
    }

    private function spin(callable $lambda, int $wait = 5): bool
    {
        $exception = null;

        for ($i = 0; $i < $wait; $i++)
        {
            try {
                if ($result = $lambda($this)) {
                    return true;
                }
            } catch (\Exception $e) {
                // do nothing
                $exception = $e;
            }

            sleep(1);
        }

        if ($exception) {
            throw $exception;
        } else {
            $backtrace = debug_backtrace();

            throw new \Exception(
                "Timeout thrown by " . $backtrace[1]['class'] ?? '' . "::" . $backtrace[1]['function'] ?? '' . "()\n" .
                $backtrace[1]['file'] ?? '' . ", line " . $backtrace[1]['line'] ?? ''
            );
        }
    }

    /**
     * @Given /^I am logged in as "([^"]*)"$/
     */
    public function iAmAuthenticatedAs($username) {
       $this->iAmLoggedInAsWithPassword($username, 'admin%1');
    }

    /**
     * @Given /^I am logged in as "([^"]*)" with password "([^"]*)"$/
     */
    public function iAmLoggedInAsWithPassword($username, $password)
    {
        $this->visit('/bolt/login');
        $this->fillField('username', $username);
        $this->fillField('password', $password);
        $this->pressButton('Log in');
    }

    /**
     * @Then /^I logout$/
     */
    public function iLogout()
    {
        $this->visit('/bolt/logout');
    }

    /**
     * @Given /^I wait (\d+) second(?:|s)$/
     */
    public function iWaitSeconds($seconds)
    {
        $this->getSession()->wait(1000*$seconds);
    }

    /**
     * @Given /^I should see at least (\d+) "([^"]*)" elements$/
     */
    public function iShouldSeeAtLeastElements($number, $element)
    {
        $foundElements = $this->getSession()->getPage()->findAll('css', $element);
        if(intval($number) > count($foundElements)){
            $message = sprintf('%d %s found on the page, but should be not less than %d.', count($foundElements), $element, $number);

            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @When /^I click "([^"]*)"$/
     */
    public function iClick($element)
    {
        $foundElement = $this->getSession()->getPage()->find('css', $element);

        $foundElement->click();
    }

    /**
     * @When I scroll :element into view
     */
    public function iScrollElementIntoView($element){
        $this->getSession()->getPage()->find('css', $element)->focus();
        $this->getSession()->executeScript("$(':focus')[0].scrollIntoView(true);window.scrollBy(0,-50);");
    }

    /**
     * iSwitchToTheWindow
     * @When /^(?:|I )switch to (?:window|tab) "(?P<nb>\d+)"$/
     */
    public function iSwitchToTheWindow($id=0){
        $this->getSession()->switchToWindow($this->getSession()->getWindowNames()[$id]);
    }

    /**
     * @Then /^I should see (\d+) "([^"]*)" elements in the "([^"]*)" element$/
     */
    public function iShouldSeeElementsInTheElement($number, $element, $parent)
    {
        $foundElements = $this->getSession()->getPage()->find('css', $parent)->findAll('css', $element);
        if(intval($number) !== count($foundElements)){
            $message = sprintf('%d %s found on the page, but should be not less than %d.', count($foundElements), $element, $number);

            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^the "([^"]*)" button should be disabled$/
     */
    public function theButtonShouldBeDisabled($button)
    {
        $foundButton = $this->getSession()->getPage()->find('css', $button);
        if(! $foundButton->getAttribute('disabled')) {
            $message = sprintf('%s expected to be disabled, but disabled attribute is %s', $button, $foundButton->getAttribute('disabled'));
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^the "([^"]*)" button should be enabled$/
     */
    public function theButtonShouldBeEnabled($button)
    {
        $foundButton = $this->getSession()->getPage()->find('css', $button);
        if ($foundButton->getAttribute('disabled')) {
            $message = sprintf('%s expected to be enabled, but disabled attribute is %s', $button, $foundButton->getAttribute('disabled'));
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @When /^I fill "([^"]*)" element with "([^"]*)"$/
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillWith($element, $value)
    {
        $this->assertSession()->elementExists('css', $element);
        $foundElement = $this->getSession()->getPage()->find('css', $element);
        $foundElement->setValue($value);
    }

    /**
     * @Given /^the field with css "([^"]*)" should contain "([^"]*)"$/
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @throws ExpectationException
     */
    public function theFieldWithCssShouldContain($element, $value)
    {
        $this->assertSession()->elementExists('css', $element);
        $foundElement = $this->getSession()->getPage()->find('css', $element);

        $actual = $foundElement->getValue();
        $regex = '/^'.preg_quote($value, '/').'$/ui';

        if(! (bool) preg_match($regex, $actual)){
            $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $foundElement, $actual, $value);
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^I should ne on url matching "([^"]*)"$/
     * @throws ExpectationException
     */
    public function iShouldNeOnUrlMatching($pattern)
    {
        $base = $this->getMinkParameter('base_url');
        $regex = "/" . preg_quote($base, "/") . substr($pattern, 2) . '/';
        $actual = $this->getSession()->getCurrentUrl();

        if(! (bool) preg_match($regex, $actual)){
            $message = sprintf('The actual url "%s" does not match the regex %s', $actual, $regex);
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Given /^I wait for "([^"]*)" field value to change$/
     * @throws \Exception
     */
    public function iWaitForToBeFilledIn($field)
    {
        //@todo find a better way to init $web
        $web = new WebAssert($this->getSession());
        $initial = $web->fieldExists($field)->getValue();
        $this->spin(function () use ($field, $initial, $web){
            $this->assertFieldNotContains($field, $initial);
            return true;
        }, 10);
    }

}
