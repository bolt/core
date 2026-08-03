<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Backend;

use Bolt\Configuration\Config;
use Bolt\Controller\Backend\ContentEditController;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\Content;
use Bolt\Enum\Statuses;
use Bolt\Event\Listener\ContentFillListener;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\MediaRepository;
use Bolt\Repository\RelationRepository;
use Bolt\Repository\TaxonomyRepository;
use Bolt\Utils\ContentHelper;
use Bolt\Validator\ContentValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentEditControllerSaveTest extends TestCase
{
    /**
     * A save over ajax that the content validator rejects has to answer with the
     * violations as JSON, because `assets/js/app/ajax-save.js` can do nothing with a
     * re-rendered editor. This test fails if that response contract changes.
     */
    public function testAjaxSaveReturnsTheValidationViolationsAsJson(): void
    {
        $controller = $this->createController();

        $response = $controller->save(
            $this->ajaxSaveRequest(),
            new Content(),
            $this->contentValidatorRejectingWith(
                new ConstraintViolation('End datetime is required', null, [], null, 'end_datetime', null),
                // Validators built by hand often leave the property path empty.
                new ConstraintViolation('The record is not valid', null, [], null, null, null)
            )
        );

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $payload = json_decode((string) $response->getContent(), true);

        self::assertSame('danger', $payload['status']);
        self::assertSame('warning', $payload['type']);
        self::assertSame('flash_messages.notification', $payload['notification']);
        self::assertSame([
            ['property' => 'end_datetime', 'message' => 'End datetime is required'],
            ['property' => '', 'message' => 'The record is not valid'],
        ], $payload['errors']);
    }

    private function createController(): ContentEditController
    {
        $controller = new ContentEditController(
            $this->createMock(TaxonomyRepository::class),
            $this->createMock(RelationRepository::class),
            $this->createMock(ContentRepository::class),
            $this->createMock(MediaRepository::class),
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            $this->createMock(ContentFillListener::class),
            $this->createMock(EventDispatcherInterface::class),
            'en',
            $this->translatorReturningKeys(),
            $this->createMock(ContentHelper::class)
        );

        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $csrfTokenManager->method('isTokenValid')->willReturn(true);
        $controller->setCsrfTokenManager($csrfTokenManager);

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method('isGranted')->willReturn(true);

        $container = new Container();
        $container->set('security.authorization_checker', $authorizationChecker);
        $container->set('security.token_storage', $this->createMock(TokenStorageInterface::class));
        $controller->setContainer($container);

        // Of the collaborators that `setAutowire()` injects, this code path only reads the
        // config, so that is the only one set here.
        (new ReflectionProperty(TwigAwareController::class, 'config'))->setValue($controller, $this->configWithValidatorEnabled());

        return $controller;
    }

    private function configWithValidatorEnabled(): Config
    {
        $config = $this->createMock(Config::class);
        $config->method('get')->willReturnCallback(
            static fn (string $path, $default = null) => $path === 'general/validator_options/enable' ? true : $default
        );

        return $config;
    }

    private function translatorReturningKeys(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return $translator;
    }

    private function contentValidatorRejectingWith(ConstraintViolation ...$violations): ContentValidatorInterface
    {
        $contentValidator = $this->createMock(ContentValidatorInterface::class);
        $contentValidator->method('validate')->willReturn(new ConstraintViolationList($violations));

        return $contentValidator;
    }

    private function ajaxSaveRequest(): Request
    {
        $request = Request::create('/bolt/edit/1', Request::METHOD_POST, [
            '_csrf_token' => 'valid',
            '_edit_locale' => 'en',
            'status' => Statuses::DRAFT,
        ]);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return $request;
    }
}
