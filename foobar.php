<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Common\Json;
use Bolt\Common\Str;
use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Bolt\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ProfileController extends TwigAwareController implements BackendZone
{
    use CsrfTrait;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ObjectManager */
    private $em;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;





    private function validateUser(User $user, ?string $newPassword): bool
    {
        // @todo Validation should be moved to a separate UserValidator

        $usernameValidateOptions = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        // Validate username
        if (! filter_var(mb_strlen($user->getDisplayName()), FILTER_VALIDATE_INT, $usernameValidateOptions)) {
            $this->addFlash('danger', 'user.not_valid_username');
            return false;
        }

        // Validate email
        if (! filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'user.not_valid_email');
            return false;
        }

        // Validate password
        if (! empty($newPassword) && mb_strlen($newPassword) < 6) {
            $this->addFlash('danger', 'user.not_valid_password');
            return false;
        }

        return true;
    }
}
