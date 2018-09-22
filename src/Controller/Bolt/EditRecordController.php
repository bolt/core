<?php

declare(strict_types=1);

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Form\ContentType;
use Bolt\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EditRecordController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EditRecordController extends AbstractController
{
    /** @var Config */
    private $config;

    /** @var Version */
    private $version;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/edit/{id}", name="bolt_edit_record")
     */
    public function edit(Content $record = null, Request $request): Response
    {
        if (!$record) {
            $record = new Content();
            $record->setAuthor($this->getUser());
        }

        dump($record);

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(ContentType::class, $record)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        return $this->render('bolt/edit/edit.twig', [
            'record' => $record,
         ]);
    }
}
