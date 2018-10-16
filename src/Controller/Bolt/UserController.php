<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Controller\BaseController;
use Bolt\Form\ChangePasswordType;
use Bolt\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends BaseController
{
    /**
     * @Route("/profile-edit", methods={"GET", "POST"}, name="bolt_profile_edit")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'user.updated_successfully');

            return $this->redirectToRoute('bolt_profile_edit');
        }

        return $this->renderTemplate('users/edit.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-password", methods={"GET", "POST"}, name="bolt_change_password")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('security_logout');
        }

        return $this->renderTemplate('users/change_password.twig', [
            'form' => $form->createView(),
        ]);
    }
}
