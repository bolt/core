<?php


namespace Bolt\Demo;


use Bolt\Entity\Content;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
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
    public function validate(Content $content)
    {
        // handcrafted validator for the length of the title in entries
        if ($content->getContentType() === 'entries') {
            // set up a fake value that looks like an 'Entry' to validate -- this is how I see a simple solution
            // being added. Of course this is not flexible at all!
            $value = [
                'fields' => [
                    'title' => $content->getFieldValue('title')
                ]
            ];
            // handwritten validation - title >= 10 characters
            $constraints = new Collection([
                'fields' => [
                    'fields' =>  new Collection([
                        'fields' => [
                            'title' => new Length(['min' => 10])
                        ],
                        'allowExtraFields' => true
                    ])
                ],
                'allowExtraFields' => true
            ]);
            return $this->validator->validate($value, $constraints);
        }
        // everything that is not of type 'entries' is ignored for validation.
        return [];
    }
}