<?php

declare(strict_types=1);

namespace Bolt\Tests\Security;

use Bolt\Security\AuthenticationEntryPointRedirector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class AuthenticationEntryPointRedirectorTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function localeProvider(): iterable
    {
        foreach (['cs', 'de', 'fr', 'nl', 'ru', 'sk'] as $locale) {
            yield $locale => [$locale];
        }
    }

    #[DataProvider('localeProvider')]
    public function testFlashMessageIsTranslatedOnceWhenRendered(string $locale): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->with('bolt_login')->willReturn('/bolt/login');

        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);

        $response = (new AuthenticationEntryPointRedirector($this->createStub(TranslatorInterface::class), $urlGenerator))->start($request);
        $this->assertSame('/bolt/login', $response->getTargetUrl());

        $flashes = $session->getFlashBag()->get('warning');
        $this->assertSame(['You have to login in order to access this page.'], $flashes, 'The flash message must be stored untranslated.');

        $translator = new Translator($locale);
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource('xlf', dirname(__DIR__, 3) . '/translations/messages.' . $locale . '.xlf', $locale);
        $collector = new DataCollectorTranslator($translator);

        // Same filter as in _flash_messages.html.twig
        $twig = new Environment(new ArrayLoader(['flash' => '{{ message|trans|raw }}']));
        $twig->addExtension(new TranslationExtension($collector));
        $twig->render('flash', ['message' => $flashes[0]]);

        $messages = $collector->getCollectedMessages();
        $this->assertCount(1, $messages, 'The flash message must be looked up exactly once.');
        $this->assertSame(DataCollectorTranslator::MESSAGE_DEFINED, $messages[0]['state']);
        $this->assertNotSame($flashes[0], $messages[0]['translation']);
    }
}
