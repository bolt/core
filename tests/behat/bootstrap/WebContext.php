<?php

declare(strict_types=1);

namespace Context;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\WebAssert;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use SimpleXMLElement;

/**
 * Defines steps for web tests.
 * @todo: MinkContext should be extended just once. Take this out into a CommonContext.
 */
trait WebContext
{
    private $NAMED_SELECTORS = ['id_or_name', 'link_or_button', 'field', 'select',
        'checkbox', 'radio', 'file', 'optgroup', 'option', 'fieldset', 'table', 'content'];

    /**
     * @When I wait for :cssSelector
     * @param $cssSelector
     * @throws \Exception
     */
    public function iWaitFor($cssSelector)
    {
        $this->spin(function(CommonContext $context) use ($cssSelector) {
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
        $this->spin(function(CommonContext $context) use ($text) {
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
        $this->fillField('login[username]', $username);
        $this->fillField('login[password]', $password);
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
     * @throws ElementNotFoundException
     * @throws ExpectationException
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
     * @throws ElementNotFoundException
     */
    public function click($element)
    {
        $this->findElement($element)->click();
    }

    /**
     * @When /^I click the (\d+)(st|nd|rd|th) "([^"]*)"$/
     * @throws ElementNotFoundException
     */
    public function clickNth($number, $tail, $element)
    {
        $this->findAllElements($element)[$number-1]->click();
    }

    /**
     * @When I scroll the :index :element into view
     * @throws ElementNotFoundException
     */
    public function iScrollNumElementIntoView($index, $element){
        $this->findAllElements($element)[$index-1]->focus();
        $this->getSession()->executeScript("$(':focus')[0].scrollIntoView(true);window.scrollBy(0,-50);");
    }

    /**
     * @When I scroll :element into view
     * @throws ElementNotFoundException
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
     * @throws ElementNotFoundException
     * @throws ExpectationException
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
     * @Then /^the (\d+)(st|nd|rd|th) "([^"]*)" button should be disabled$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     */
    public function theButtonShouldBeDisabled($index, $tail, $button)
    {
        $foundButton =$this->findAllElements($button)[$index-1];
        if(! $foundButton->getAttribute('disabled')) {
            $message = sprintf('%s expected to be disabled, but disabled attribute is %s', $button, $foundButton->getAttribute('disabled'));
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^the (\d+)(st|nd|rd|th) "([^"]*)" button should be enabled$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     */
    public function theButtonShouldBeEnabled($index, $tail, $button)
    {
        $foundButton = $this->findAllElements($button)[$index-1];
        if ($foundButton->getAttribute('disabled')) {
            $message = sprintf('%s expected to be enabled, but disabled attribute is %s', $button, $foundButton->getAttribute('disabled'));
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @When /^I fill the (\d+)(st|nd|rd|th) "([^"]*)" element with "([^"]*)"$/
     * @throws ElementNotFoundException
     */
    public function iFillNthWith($index, $tail, $element, $value)
    {
        $foundElement = $this->findAllElements($element)[$index-1];
        $foundElement->setValue($value);
    }

    /**
        * @When /^I fill "([^"]*)" element with "([^"]*)"$/
     * @throws ElementNotFoundException
    */
    public function iFillWith($element, $value)
    {
        $foundElement = $this->findElement($element);
        $foundElement->setValue($value);
    }

    /**
     * @Given /^the (\d+)(st|nd|rd|th) field "([^"]*)" should contain "([^"]*)"$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     */
    public function theNthFieldShouldContain($index, $tail, $element, $value)
    {
        $foundElement = $this->findAllElements($element)[$index-1];

        $actual = $foundElement->getValue();
        $regex = '/^'.preg_quote($value, '/').'$/ui';

        if(! (bool) preg_match($regex, $actual)){
            $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $foundElement, $actual, $value);
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Given /^the field "([^"]*)" should contain "([^"]*)"$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     */
    public function theFieldShouldContain($element, $value)
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
        }, 20);
    }
    
    /**   
     * @Given /^I should see exactly one "([^"]*)" element$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     */
    public function iShouldSeeExactlyOneElement($selector)
    {
        $foundElements = $this->findAllElements($selector);
        if(! count($foundElements) === 1){
            $message = sprintf('%d %s found on the page, but should be one', count($foundElements), $selector);

            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the (\d+)(st|nd|rd|th) "(?P<element>[^"]*)" element$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     */
    public function assertNthElementContainsText($text, $index, $tail, $element)
    {
        $foundElement = $this->findAllElements($element)[$index-1];

        // for this, see WebAssert.php
        $actual = $foundElement->getHtml();
        $regex = '/'.preg_quote($text, '/').'/ui';

        if(! (bool) preg_match($regex, $actual))
        {
            $message = sprintf(
                'The string "%s" was not found in the HTML of the %s.',
                $text,
                $element
            );
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     */
    public function assertElementContainsText($element, $text)
    {
        $this->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));
    }

    /**
     * @When /^I hover over the "([^"]*)" element$/
     * @throws ElementNotFoundException
     */
    public function iHoverOverTheElement($selector)
    {
        $element = $this->findElement($selector);
        $element->mouseOver();
    }

    /**
     * @Given /^the "([^"]*)" field should be filled in$/
     * @throws ExpectationException
     */
    public function theFieldShouldBeFilledIn($selector)
    {
        $actual = $this->findElement($selector)->getValue();
        $notExpected = '';

        $regex = '/^'.preg_quote($notExpected, '/').'$/ui';

        $message = sprintf('The field "%s" value is "%s", but it should not be.', $selector, $actual);

        if(preg_match($regex, $actual)) {
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }
    }

    /**
     * @Then /^the "([^"]*)" field should have "([^"]*)" attribute$/
     * @throws ExpectationException
     */
    public function theFieldShouldHaveAttribute($selector, $attribute)
    {
        $element = $this->findElement($selector);

        $atts_array = current((array) new SimpleXMLElement("<element $attribute />"));

        $name = array_key_first($atts_array);
        $value = $atts_array[$name];

        if(! $element->hasAttribute($name))
        {
            $message = sprintf('The field "%s" has no attribute "%s"', $selector, $attribute);
            throw new ExpectationException($message, $this->getSession()->getDriver());
        } else if ($element->getAttribute($name) !== $value)
        {
            $message = sprintf('The field "%s" has attribute "%s" with value "%s", but "%s" expected',
                $selector, $name, $element->getAttribute($name), $value);
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }

    }

    private function findAllElements($selector, $parent = null)
    {
        if ($parent === null) {
            $parent = $this->getSession()->getPage();
        }

        $elements = null;
        // by default, look for named selector
        // for documentation on this, check http://mink.behat.org/en/latest/guides/traversing-pages.html#named-selectors
        foreach ($this->NAMED_SELECTORS as $selector_type) {
            $elements = $parent->findAll('named', [$selector_type, $selector]);
            if (! empty($elements)) break;
        }

        if (empty($elements)) {
            // if not, try a css selector
            $elements = $parent->findAll('css', $selector);
        }

        if($elements === null)
        {
            throw new  ElementNotFoundException($this->getSession()->getDriver(), 'Element', 'named|css', $selector);
        }

        return $elements;
    }

    private function findElement($selector): NodeElement
    {
        // by default, look for named selector
        // for documentation on this, check http://mink.behat.org/en/latest/guides/traversing-pages.html#named-selectors
        foreach($this->NAMED_SELECTORS as $selector_type) {
            $element = $this->getSession()->getPage()->find('named', [$selector_type, $selector]);
            if($element != null) break;
        }
        if($element === null)
        {
            // if not, try a css selector
            $element = $this->getSession()->getPage()->find('css', $selector);
        }

        if($element === null)
        {
            throw new  ElementNotFoundException($this->getSession()->getDriver(), 'Element', 'named|css', $selector);
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
