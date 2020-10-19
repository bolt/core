<?php

declare(strict_types=1);

namespace Bolt\Validator;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContentValidator implements ContentValidatorInterface
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

    private function getFieldConstraints($contentType): Collection
    {
        $fieldConstraints = [];
        /** @var FieldType $fieldType */
        foreach ($contentType->get('fields', []) as $fieldName => $fieldType) {
            $fieldConstraintConfig = $fieldType->get('constraints', collect([]))->toArray();
//            TODO: handle set and collection
            if (\count($fieldConstraintConfig) > 0) {
                $constraints = $this->loader->parseNodes($fieldConstraintConfig);
                $fieldConstraints[$fieldName] = $constraints;
            }

            // handle sets
            if ($fieldType->get('type') === 'set') {
                // recursively collect constraints
                $fieldConstraints[$fieldName] = $this->getFieldConstraints($fieldType);
            }
        }
        return new Collection([
            'fields' => $fieldConstraints,
            'allowExtraFields' => true,
        ]);
    }

    private function getConstraints($contentTypeName)
    {
        $contentTypes = $this->config->get('contenttypes');

        /** @var ContentType $contentType */
        $contentType = $contentTypes->get($contentTypeName);

        $fieldConstraints = $this->getFieldConstraints($contentType);

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
//                'fields' => new Collection([
//                    'fields' => $fieldConstraints,
//                    'allowExtraFields' => true,
//                ]),
                'fields' => $fieldConstraints,
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
