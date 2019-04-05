<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\CsrfTrait;
use Bolt\Controller\TwigAwareController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Webmozart\PathUtil\Path;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class FileEditController extends TwigAwareController
{
    use CsrfTrait;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/file-edit/{area}", name="bolt_file_edit", methods={"GET"})
     */
    public function edit(string $area, Request $request): Response
    {
        $file = $request->query->get('file');
        if (mb_strpos($file, '/') !== 0) {
            $file = '/' . $file;
        }
        $basepath = $this->config->getPath($area);
        $filename = Path::canonicalize($basepath . '/' . $file);
        $contents = file_get_contents($filename);

        $context = [
            'area' => $area,
            'file' => $file,
            'contents' => $contents,
        ];

        return $this->renderTemplate('@bolt/finder/editfile.html.twig', $context);
    }

    /**
     * @Route("/file-edit/{area}", name="bolt_file-edit_post", methods={"POST"}, requirements={"file"=".+"})
     */
    public function save(Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $this->validateCsrf($request, 'editfile');

        $file = $request->request->get('file');
        $area = $request->request->get('area');
        $contents = $request->request->get('editfile');
        $extension = Path::getExtension($file);

        $url = $urlGenerator->generate('bolt_file_edit', [
            'area' => $area,
            'file' => $file,
        ]);

        if (in_array($extension, ['yml', 'yaml'], true) && ! $this->verifyYaml($contents)) {
            $context = [
                'area' => $area,
                'file' => $file,
                'contents' => $contents,
            ];

            return $this->renderTemplate('@bolt/finder/editfile.html.twig', $context);
        }

        $basepath = $this->config->getPath($area);
        $filename = Path::canonicalize($basepath . '/' . $file);

        // @todo maybe replace file_put_contents with some more abstract Filesystem?
        if (file_put_contents($filename, $contents)) {
            $this->addFlash('success', 'editfile.updated_successfully');
        } else {
            $this->addFlash('warn', 'editfile.could_not_write');
        }

        return new RedirectResponse($url);
    }

    private function verifyYaml(string $yaml): bool
    {
        $yamlParser = new Parser();
        try {
            $yamlParser->parse($yaml);
        } catch (ParseException $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        return true;
    }
}
