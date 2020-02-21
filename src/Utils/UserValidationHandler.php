<?php

namespace Bolt\Utils;

use Symfony\Component\HttpFoundation\RequestStack;

class UserValidationHandler
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function handle(UserValidator $validator)
    {
        foreach($validator->getValidationErrors() as $error)
        {
            if($error === UserValidator::DISPLAY_NAME_ERROR)
            {
                $this->addFlash('danger', 'user.not_valid_display_name');
            }

            if ($error === UserValidator::PASSWORD_ERROR)
            {
                $this->addFlash('danger', 'user.not_valid_password');
            }

            if ($error === UserValidator::EMAIL_ERROR)
            {
                $this->addFlash('danger', 'user.not_valid_email');
            }
        }
    }

    private function addFlash(string $type, string $message)
    {
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add($type, $message);
    }
}
