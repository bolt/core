<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Configuration\Config;
use Bolt\Entity\FieldTranslation;
use Bolt\Repository\FieldRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipArchive;

class ContentTranslationController extends AbstractController implements BackendZoneInterface
{
    const NON_TEXT_FIELDS = [
        'file',
        'image',
    ];

    /**
     * @var Config $config
     */
    protected $config;

    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * @var FieldRepository $fieldRepository
     */
    protected $fieldRepository;

    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @var array $locales
     */
    protected $locales;

    /**
     * @var string $defaultLocale
     */
    protected $defaultLocale;

    /**
     * @var string $currentHost
     */
    protected $currentHost;

    /**
     * @param Config                 $config
     * @param EntityManagerInterface $entityManager
     * @param FieldRepository        $fieldRepository
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        Config                 $config,
        EntityManagerInterface $entityManager,
        FieldRepository        $fieldRepository,
        TranslatorInterface    $translator
    )
    {
        $this->config          = $config;
        $this->entityManager   = $entityManager;
        $this->fieldRepository = $fieldRepository;
        $this->translator      = $translator;
    }

    /**
     * @return Response
     *
     * @Route(
     *     "/content-translations",
     *     name="bolt_content_translations_index",
     *     methods={"GET"},
     * )
     */
    public function indexAction(): Response
    {
        $form = $this->getForm();

        return $this->renderForm(
            '@bolt/translations/index.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     *
     * @Route(
     *     "/content-translations/export",
     *     name="bolt_content_translations_export",
     *     methods={"GET"},
     * )
     */
    public function exportAction(Request $request): Response
    {
        $this->currentHost   = $request->getHttpHost();
        $this->locales       = $this->config->getLocales();
        $this->defaultLocale = $this->config->getDefaultLocale();
        $catalogues          = $this->generateTranslationCatalogues();

        return $this->createZipFile();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function generateTranslationCatalogues()
    {
        $catalogues          = $this->createCatalogues();
        $translations        = $this->getExistingTranslations();
        $groupedTranslations = $this->groupTranslations($translations);

        $this->addTranslationsToCatalogues($catalogues, $groupedTranslations);
        $this->dumpCatalogues($catalogues);

        return $catalogues;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     *
     * @Route(
     *     "/content-translations/import",
     *     name="bolt_content_translations_import",
     *     methods={"GET", "POST"},
     * )
     */
    public function importAction(Request $request): Response
    {
        $notFound = [];
        $form     = $this->getForm();

        $form->handleRequest();

        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $form = $request->files->get('form');

            if (isset($form['files'])) {
                $files = $form['files'];

                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $notFound = [...$notFound, ...$this->processXlfTranslations($file)];
                    }
                }

                if (count($notFound)) {
                    foreach ($notFound as $locale => $fields) {
                        $notFoundValues = implode(', <br />', $fields);

                        $request->getSession()->getFlashBag()->add(
                            'warning',
                            $this->translator->trans(
                                'import.not_found_warning',
                                [
                                    '{{ locale }}' => $locale,
                                    '{{ fields }}' => $notFoundValues,
                                ],
                                'validators'
                            )
                        );
                    }
                }
            }

            return new RedirectResponse($this->generateUrl('bolt_content_translations_index'));
        }

        throw new Exception('Could not process the imported translation file.');
    }

    /**
     * @return FormInterface
     */
    private function getForm(): FormInterface
    {
        $formFactory = Forms::createFormFactory();
        $form        = $formFactory->createBuilder()
            ->setAction($this->generateUrl('bolt_content_translations_import'))
            ->add(
                'files',
                FileType::class,
                [
                    'label'    => $this->translator->trans('bolt_content_translations_files', [], 'messages'),
                    'required' => true,
                    'multiple' => true,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => $this->translator->trans('bolt_content_translations_send', [], 'messages'),
                    'attr'  => [
                        'class' => 'btn btn-secondary btn-small',
                    ],
                ]
            )
            ->getForm()
        ;

        return $form;
    }

    /**
     * @return array
     */
    private function createCatalogues(): array
    {
        $catalogues = [];

        foreach ($this->locales as $locale) {
            $catalogues[$locale] = new MessageCatalogue($locale);
        }

        return $catalogues;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getExistingTranslations(): array
    {
        $translations = [];
        $allFields    = $this->fieldRepository->findAll();

        foreach ($allFields as $field) {
            if ($field->isTranslatable()) {
                $fieldTranslations = [];

                foreach ($field->getTranslations() as $translation) {
                    $values = $translation->getValue();

                    if (count($values) <= 1) {
                        $value = $values[0];

                        if ($value) {
                            $fieldTranslations[$translation->getLocale()] = [
                                'value' => $value,
                                'type'  => $field->getType(),
                            ];
                        }
                    } else if (!in_array($field->getType(), self::NON_TEXT_FIELDS)) {
                        throw new Exception('Unknown field type give, please contact a bolt developer to check this seemingly special case.');
                    }
                }

                if (count($fieldTranslations)) {
                    $translations[$field->getId()] = $fieldTranslations;
                }
            }
        }

        return $translations;
    }

    /**
     * @param array $translations
     * @return array
     */
    private function groupTranslations(array $translations): array
    {
        $groupedTranslations = [];

        foreach ($translations as $id => $translation) {
            foreach ($this->locales as $locale) {
                if (isset($translation[$this->defaultLocale])) {
                    if (!isset($groupedTranslations[$locale])) {
                        $groupedTranslations[$locale] = [];
                    }

                    $value = $translation[$this->defaultLocale]['value'];

                    // We encode the string to avoid errors with special characters that cannot be used as an array key
                    $key = base64_encode($value);

                    if (!isset($groupedTranslations[$locale][$key])) {
                        $groupedTranslations[$locale][$key] = [
                            'text' => $translation[$locale]['value'] ?? $translation[$this->defaultLocale]['value'],
                            'type' => $translation[$locale]['type'] ?? $translation[$this->defaultLocale]['type'],
                            'ids'  => [$id],
                        ];
                    } else {
                        $groupedTranslations[$locale][$key]['ids'] = array_merge($groupedTranslations[$locale][$key]['ids'], [$id]);
                    }
                }
            }
        }

        return $groupedTranslations;
    }

    /**
     * @param array $catalogues
     * @param array $translations
     * @return void
     */
    private function addTranslationsToCatalogues(array $catalogues, array $translations): void
    {
        /**
         * @var MessageCatalogue $catalogue
         */
        foreach ($catalogues as $locale => $catalogue) {
            foreach ($translations[$locale] as $key => $translation) {
                $decodedKey          = base64_decode($key);
                $translationText     = $translation['text'];
                $translationType     = $translation['type'];
                $translationTextJson = json_decode($translationText);

                if (!$translationTextJson) {
                    $this->addTranslationToCatalogues($catalogue, $decodedKey, $translationText, $translation['ids'], $translationText, $translationType);
                } else {
                    foreach ($translationTextJson as $key => $textParts) {
                        $this->addTranslationToCatalogues($catalogue, $textParts, $textParts, $translation['ids'], $translationText, $translationType, $key);
                    }
                }
            }
        }
    }

    /**
     * @param MessageCatalogue $catalogue
     * @param string           $decodedKey
     * @param string           $translationText
     * @param array            $translationIds
     * @param string|null      $originalText
     * @param string|null      $translationType
     * @param string|null      $postfix
     * @return void
     */
    private function addTranslationToCatalogues(MessageCatalogue $catalogue, string $decodedKey, string $translationText, array $translationIds, string $originalText = null, string $translationType = null, string $postfix = null)
    {
        if ($translationText) {
            $catalogue->add(
                [
                    $decodedKey => $translationText,
                ],
                $this->currentHost
            );

            $idNotes = [];

            foreach ($translationIds as $id) {
                $idNotes[] = [
                    'category' => 'id',
                    'content'  => $id,
                ];

                $idNotes[] = [
                    'category' => 'type',
                    'content'  => $translationType,
                ];

                if ($postfix) {
                    $idNotes[] = [
                        'category' => 'original',
                        'content'  => $originalText,
                    ];

                    $idNotes[] = [
                        'category' => 'part',
                        'content'  => $postfix,
                    ];
                }
            }

            $catalogue->setMetadata(
                $decodedKey,
                [
                    'notes' => $idNotes,
                ],
                $this->currentHost
            );
        }
    }

    /**
     * @param array $catalogues
     * @return void
     */
    private function dumpCatalogues(array $catalogues): void
    {
        $dumper = new XliffFileDumper();

        foreach ($catalogues as $catalogue) {
            $dumper->dump(
                $catalogue,
                [
                    'path'          => sys_get_temp_dir(),
                    'xliff_version' => '2.0',
                ]
            );
        }
    }

    /**
     * @return Response
     */
    private function createZipFile(): Response
    {
        $zip     = new ZipArchive();
        $files   = glob(sys_get_temp_dir() . '/' . $this->currentHost . '.*.xlf');
        $zipName = $this->currentHost . '.zip';

        $zip->open(
            $zipName,
            ZipArchive::CREATE
        );

        foreach ($files as $file) {
            $zip->addFromString(basename($file), file_get_contents($file));

            @unlink($file);
        }

        $zip->close();

        $response = new Response(file_get_contents($zipName));

        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        @unlink($zipName);

        return $response;
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    private function processXlfTranslations(UploadedFile $file): array
    {
        $fileContent    = file_get_contents($file->getPathname());
        $crawler        = new Crawler($fileContent);
        $xliff          = $crawler->filterXPath('//xliff');
        $targetLanguage = $xliff->attr('trgLang');
        $translations   = $crawler->filterXPath('//xliff/file/unit')->each(
            function (Crawler $parentCrawler) {
                $results  = [];
                $source   = $parentCrawler->filterXPath('node()/segment/source')->text();
                $original = $parentCrawler->filterXPath('node()/notes/note[@category="original"]');
                $part     = $parentCrawler->filterXPath('node()/notes/note[@category="part"]');
                $ids      = $parentCrawler->filterXPath('node()/notes/note[@category="id"]')->each(
                    function (Crawler $parentCrawler) {
                        return $parentCrawler->text();
                    }
                );

                foreach ($ids as $id) {
                    $results['ids'][$id] = $parentCrawler->filterXPath('node()/segment/target')->text();
                }

                if ($original->count()) {
                    $results['original'] = $original->text();
                }

                if ($part->count()) {
                    $results['part'] = $part->text();
                }

                $results['source'] = $source;

                return $results;
            }
        );

        $groupedTranslations = $this->groupXlfTranslations($translations);

        return $this->persistXlfTranslations($groupedTranslations, $targetLanguage);
    }

    /**
     * @param array $translations
     * @return array
     */
    private function groupXlfTranslations(array $translations): array
    {
        $groupedTranslations = [];

        foreach ($translations as $translation) {
            if (isset($translation['original'])) {
                $groupedTranslations[implode('__', array_keys($translation['ids']))][] = $translation;
            } else {
                $groupedTranslations[implode('__', array_keys($translation['ids']))] = $translation;
            }
        }

        foreach ($groupedTranslations as $key => $groupedTranslation) {
            $mergedTranslation = [
                'ids' => [],
            ];

            foreach ($groupedTranslation as $translationPart) {
                if (isset($translationPart['original'])) {
                    $mergedTranslation['source'] = $translationPart['original'];

                    foreach ($translationPart['ids'] as $id => $translation) {
                        if (!isset($mergedTranslation['ids'][$id])) {
                            $mergedTranslation['ids'][$id] = json_decode($translationPart['original']);
                        }

                        $mergedTranslation['ids'][$id]->{$translationPart['part']} = $translation;
                    }
                } else {
                    $mergedTranslation['source'] = $groupedTranslation['source'];
                }
            }

            if (count($mergedTranslation['ids'])) {
                $groupedTranslations[$key] = $mergedTranslation;
            }
        }

        return $groupedTranslations;
    }

    /**
     * @param array  $translations
     * @param string $targetLanguage
     * @return array
     */
    private function persistXlfTranslations(array $translations, string $targetLanguage): array
    {
        $notFound = [];

        foreach ($translations as $translationGroup) {
            $source = $translationGroup['source'];

            foreach ($translationGroup['ids'] as $id => $value) {
                if (is_object($value)) {
                    $value = json_encode($value);
                }

                $field = $this->fieldRepository->findOneById($id);

                if (!$field) {
                    $field = $this->fieldRepository->findOneByTranslationValue($source);
                }

                if ($field) {
                    $fieldTranslation = $this->getFieldTranslation($field->getTranslations(), $targetLanguage);

                    $fieldTranslation->setValue($value);
                    $fieldTranslation->setLocale($targetLanguage);
                    $field->addTranslation($fieldTranslation);

                    $this->entityManager->persist($field);
                } else {
                    $notFound[$targetLanguage][] = $value;
                }
            }
        }

        $this->entityManager->flush();

        return $notFound;
    }

    /**
     * @param Collection $translations
     * @param string     $locale
     * @return FieldTranslation
     */
    private function getFieldTranslation(Collection $translations, string $locale): FieldTranslation
    {
        $translation = array_values(
            array_filter(
                $translations->getValues(),
                function (FieldTranslation $translation) use ($locale) {
                    return $translation->getLocale() === $locale;
                }
            )
        );

        if ($translation) {
            return $translation[0];
        }

        return new FieldTranslation();
    }
}
