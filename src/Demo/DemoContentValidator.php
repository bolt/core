<?php

declare(strict_types=1);

namespace Bolt\Demo;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Validator\ContentTypeConstraintLoader;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DemoContentValidator implements \Bolt\Validator\ContentValidatorInterface
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var Config */
    private $config;

    /** @var ContentTypeConstraintLoader */
    private $loader;

    public function __construct(ValidatorInterface $validator, Config $config)
    {
        $this->validator = $validator;
        $this->config = $config;
        $this->loader = new ContentTypeConstraintLoader();
    }

    private function getConstraints($contentTypeId)
    {
        $contentTypes = $this->config->get('contenttypes');

        /** @var ContentType $contentType */
        $contentType = $contentTypes->get($contentTypeId);

        $fieldConstraints = [];

        /** @var FieldType $fieldType */
        foreach ($contentType->get('fields', []) as $fieldName => $fieldType) {
            $fieldConstraintConfig = $fieldType->get('constraints', collect([]))->toArray();
//            TODO: handle set and collection
            if (\count($fieldConstraintConfig) > 0) {
                $constraints = $this->loader->parseNodes($fieldConstraintConfig);
                $fieldConstraints[$fieldName] = $constraints;
            }
        }

        $relationshipConstraints = [];
        foreach ($contentType->get('relations', []) as $relationType => $relationConfig) {
            $relationConstraintConfig = $relationConfig->get('limit', collect([]))->toArray();
            if (\count($relationConstraintConfig) > 0) {
                // pass contents of 'limit' node to Symfony CountConstraint
                $relationshipConstraints[$relationType] = $this->loader->parseNodes([['Count' => $relationConstraintConfig]]);
            }
        }

        // Note 'fields' is both attribute of collection constraint and a property
        // of the data that is being validated
        return new Collection([
            'fields' => [
                // 'fields' property of bolt Content class / form
                'fields' => new Collection([
                    'fields' => $fieldConstraints,
                    'allowExtraFields' => true,
                ]),
                'relationship' => new Collection([
                    'fields' => $relationshipConstraints,
                    'allowExtraFields' => true,
                ]),
            ],
            'allowExtraFields' => true,
        ]);
    }

    private function relationsToMap($relations)
    {
        $result = [];
        /** @var Relation $relation */
        foreach ($relations as $relation) {
            $to = $relation->getToContent();
            $typeName = $to->getContentType();
            if (! isset($result[$typeName])) {
                $result[$typeName] = [];
            }
            $result[$typeName][] = $to->getId();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Content $content)
    {
        $constraints = $this->getConstraints($content->getContentType());

        if ($constraints) {
            // set up a value that maps to the fields as used in the back-end forms, and can be passed to the
            // validate function of the Symfony Validator
            $value = [
                'fields' => $content->getFieldValues(),
                'taxonomy' => $content->getTaxonomyValues(),
                'relationship' => $this->relationsToMap($content->getRelationsFromThisContent()),
            ];

            return $this->validator->validate($value, $constraints);
        }

        // if no constraints are found -> always pass
        return [];
    }
}
