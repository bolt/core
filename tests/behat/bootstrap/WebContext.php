<?php

declare(strict_types=1);

namespace Context;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
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
            return !is_null($this->findElement($cssSelector));
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
     * @Given /^I should see at least (\d+) "([^"]*)" elements$/
     */
    public function iShouldSeeAtLeastElements($number, $element)
    {
        $foundElements = $this->findAllElements($element);
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
        $this->findElement($element)->click();
    }

    /**
     * @When I scroll :element into view
     */
    public function iScrollElementIntoView($element){
        $this->findElement($element)->focus();
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
        $parent = $this->findElement($parent);
        $foundElements = $this->findAllElements($element, $parent);
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
        $foundButton =$this->findElement($button);
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
        $foundButton = $this->findElement($button);
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
        $foundElement = $this->findElement($element);
        $foundElement->setValue($value);
    }

    /**
     * @Given /^the field with css "([^"]*)" should contain "([^"]*)"$/
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @throws ExpectationException
     */
    public function theFieldWithCssShouldContain($element, $value)
    {
        $foundElement = $this->findElement($element);

        $actual = $foundElement->getValue();
        $regex = '/^'.preg_quote($value, '/').'$/ui';

        if(! (bool) preg_match($regex, $actual)){
            $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $foundElement, $actual, $value);
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^I should be on url matching "([^"]*)"$/
     * @throws ExpectationException
     */
    public function iShouldBeOnUrlMatching($pattern)
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
    private function findAllElements($selector, $parent = null)
    {
        if($parent === null)
        {
            $parent = $this->getSession()->getPage();
        }
        // by default, look for named selector

        try {
            $elements = $parent->findAll('named', $selector);
        } catch (\Exception $e) {
            // if not, try a css selector
            try {
                $elements = $parent->findAll('css', $selector);
            } catch(\Exception $e) {
                throw new  ElementNotFoundException($this->getSession()->getDriver(), null, 'named|css', $selector);
            }
        }

        return $elements;
    }

    private function findElement($selector)
    {
        // by default, look for named selector
        try {
            $element = $this->getSession()->getPage()->find('named', $selector);
        } catch (\Exception $e) {
            // if not, try a css selector
            try {
                $element = $this->getSession()->getPage()->find('css', $selector);
            } catch(\Exception $e) {
                throw new  ElementNotFoundException($this->getSession()->getDriver(), null, 'named|css', $selector);
            }
        }

        return $element;
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
}
