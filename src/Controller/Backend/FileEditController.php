<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Webmozart\PathUtil\Path;

/**
 * Class FileEditController.
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class FileEditController extends BaseController
{
    /**
     * @Route("/file-edit/{area}", name="bolt_file_edit", methods={"GET"})
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editFile(string $area, Request $request): Response
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

        return $this->renderTemplate('finder/editfile.html.twig', $context);
    }

    /**
     * @Route("/file-edit/{area}", name="bolt_file-edit_post", methods={"POST"}, requirements={"file"=".+"})
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editFilePost(Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $token = new CsrfToken('editfile', $request->request->get('_csrf_token'));

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

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

            return $this->renderTemplate('finder/editfile.html.twig', $context);
        }

        $basepath = $this->config->getPath($area);
        $filename = Path::canonicalize($basepath . '/' . $file);

        if (file_put_contents($filename, $contents)) {
            $this->addFlash('success', 'editfile.updated_successfully');
        } else {
            $this->addFlash('warn', 'editfile.could_not_write');
        }

        return new RedirectResponse($url);
    }

    private function verifyYaml(string $yaml): bool
    {
        $yamlparser = new Parser();
        try {
            $yamlparser->parse($yaml);
        } catch (ParseException $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        return true;
    }
}
