<?php

declare(strict_types=1);

namespace Bolt\Demo;

use Bolt\Entity\Content;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DemoContentValidator implements \Bolt\Validator\ContentValidatorInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Content $content, array $relations)
    {
        // manually created validator for showcases
        if ($content->getContentType() === 'showcases') {
            // set up a value that maps to the fields as used in the back-end forms, and can be passed to the
            // validate function of the Symfony Validator
            $value = [
                'fields' => $content->getFieldValues(),
                'taxonomy' => $content->getTaxonomyValues(),
                'relationship' => $relations,
            ];

            // handwritten constraints - keep as is for really small validation needs, and otherwise
            // devise a system to read validation from contenttypes.yaml
            $constraints = new Collection([
                // 'fields' attribute of collection constraint
                'fields' => [
                    // 'fields' property of bolt Content class / form
                    'fields' => new Collection([
                        // 'fields' attribute of collection constraint
                        'fields' => [
                            'title' => new Length(['min' => 10]),
                        ],
                        'allowExtraFields' => true,
                    ]),
                    'taxonomy' => new Collection([
                        // 'fields' attribute of collection constraint
                        'fields' => [
                            // allow max 2 categories
                            'categories' => new Count(['max' => 2]),
                        ],
                        'allowExtraFields' => true,
                    ]),
                    // 'relationship' property of form - this is not part of the Content class
                    'relationship' => new Collection([
                        // 'fields' attribute of collection constraint
                        'fields' => [
                            'pages' => new Count(['min' => 2]),
                        ],
                        'allowExtraFields' => true,
                    ]),
                ],
                'allowExtraFields' => true,
            ]);

            return $this->validator->validate($value, $constraints);
        }
        // everything that was not matched before is ignored for validation -> this means it will always pass
        return [];
    }
}
