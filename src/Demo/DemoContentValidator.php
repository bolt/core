<?php


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
     * @inheritDoc
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
                'relationship' => $relations
            ];

            // handwritten constraints - keep as is for really small validation needs, and otherwise
            // devise a system to read validation from contenttypes.yaml
            $constraints = new Collection([
                'fields' => [ // <-- 'fields' attribute of collection constraint
                    'fields' =>  new Collection([ // <-- 'fields' property of bolt Content class / form
                        'fields' => [ // <-- 'fields' attribute of collection constraint
                            'title' => new Length(['min' => 10])
                        ],
                        'allowExtraFields' => true
                    ]),
                    'taxonomy' => new Collection([
                        'fields' => [ // <-- 'fields' attribute of collection constraint
                            'categories' => new Count(['max' => 2]) // allow max 2 categories
                        ],
                        'allowExtraFields' => true
                    ]),
                    'relationship' => new Collection([ // <-- 'relationship' property of form - this is not part of the Content class
                        'fields' => [ // <-- 'fields' attribute of collection constraint
                            'pages' => new Count(['min' => 2])
                        ],
                        'allowExtraFields' => true
                    ]),
                ],
                'allowExtraFields' => true
            ]);
            return $this->validator->validate($value, $constraints);
        }
        // everything that was not matched before is ignored for validation -> this means it will always pass
        return [];
    }
}