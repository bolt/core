<?php

declare(strict_types=1);

namespace Context;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Driver\BrowserKitDriver;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Goutte\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    private const HTTP_HOST = '127.0.0.1:8088';

    /**
     * @When I wait for :cssSelector
     * @param $cssSelector
     * @throws \Exception
     */
    public function iWaitFor($cssSelector)
    {
        $this->spin(function(FeatureContext $context) use ($cssSelector) {
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
        $this->spin(function(FeatureContext $context) use ($text) {
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
        $this->spin(function(FeatureContext $context) use ($text) {
            $context->assertPageNotContainsText($text);
            return true;
        });
    }

    public function spin(callable $lambda, int $wait = 5): bool
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
     * @When I send a :method request to :path
     * @When I send a :method request to :path with body:
     */
    public function iSendARequestToWithBody($method, $path, PyStringNode $body = null)
    {
        $body = $body instanceof PyStringNode ? $body->getRaw() : '{}';
        $this->sendRequest($method, $path, $body);
    }

    /**
     * @When I send a :method request to :path with attachment :file
     */
    public function iSendARequestToWithAttachment($method, $path, $file)
    {
        $headers = [
            'CONTENT_TYPE' => 'multipart/form-data',
        ];

        $files = [];
        if (file_exists('features/fixtures/files/' . $file)) {
            $files = ['data' => new UploadedFile('features/fixtures/files/' . $file, $file)];
        }
        $this->sendRequest($method, $path, '{}', $headers, $files);
    }

    /**
     * @Then the header :name should be equal to :value
     */
    public function theHeaderShouldBeEqualTo($name, $value)
    {
        $response = $this->getBrowser()->getResponse();
        $actual = $response->headers->get($name);
        if (strtolower($value) !== strtolower($actual)) {
            throw new \RuntimeException("Expected header '$value', but got '$actual'");
        }
    }

    /**
     * @Then the response should contain json:
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();

        if (!$matcher->match($this->getPageContent(), $jsonString->getRaw())) {
            throw new \RuntimeException($matcher->getError());
        }
    }

    /**
     * @Then print last response headers
     */
    public function printLastResponseHeaders(): string
    {
        $response = $this->getBrowser()->getResponse();
        $headers = $response->headers->all();

        $text = '';
        foreach ($headers as $name => $value) {
            $text .= $name . ': ' . $response->headers->get($name) . "\n";
        }
        echo $text;
    }

    /**
     * @Then save output to file
     */
    public function saveOutputToFile()
    {
        $dir = __DIR__.'/../../var/log/api-reports';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $dir.'/output.html';
        if (file_exists($file)) {
            unlink($file);
        }

        file_put_contents($file, $this->getPageContent());

        return true;
    }

    /**
     * @return string
     */
    protected function getPageContent()
    {
        return $this->getSession()->getPage()->getContent();
    }

    /**
     * @param $method
     * @param $path
     * @param string $body
     * @param array $headers
     * @param array $files
     */
    private function sendRequest($method, $path, $body = '{}', $headers = [], $files = [])
    {
        $method = strtoupper($method);

        $this->getBrowser()->request($method, $path, [], $files, $headers, $body);
    }

    /**
     * @BeforeScenario @allowRedirects
     */
    public function allowRedirects()
    {
        $this->getBrowser()->followRedirects(false);
    }

    /**
     * @return Client
     */
    private function getBrowser()
    {
        if (!$this->getSession()->isStarted()) {
            $this->getSession()->start();
        }
        $driver = $this->getSession()->getDriver();
        if ( ! $driver instanceof BrowserKitDriver) {
            throw new \RuntimeException('Unsupported driver. BrowserKit driver is required.');
        }

        /**
         * @var Client $client
         */
        $client = $driver->getClient();
        $client->setServerParameter('HTTP_HOST', self::HTTP_HOST);

        return $client;
    }
}
