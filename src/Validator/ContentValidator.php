<?php

declare(strict_types=1);

namespace Bolt\Validator;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * This is the default validator for Bolt, it uses symfony/validator for almost all validation.
 */
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

    private function getFieldConstraints($contentType)
    {
        // exception for single fields in collections, they don't have a 'fields' nesting layer
        if ($contentType->get('fields') === null && $contentType->get('constraints') !== null) {
            $fieldConstraintConfig = $contentType->get('constraints', collect([]))->toArray();
            if (\count($fieldConstraintConfig) > 0) {
                return $this->loader->parseNodes($fieldConstraintConfig);
            }

            return null;
        }
        $fieldConstraints = [];
        /** @var FieldType $fieldType */
        foreach ($contentType->get('fields', []) as $fieldName => $fieldType) {
            $fieldConstraintConfig = $fieldType->get('constraints', collect([]))->toArray();
            if (\count($fieldConstraintConfig) > 0) {
                $constraints = $this->loader->parseNodes($fieldConstraintConfig);
                $fieldConstraints[$fieldName] = $constraints;
            }

            // handle sets
            if ($fieldType->get('type') === 'set') {
                // recursively collect constraints
                $setFieldConstraints = $this->getFieldConstraints($fieldType);
                if ($setFieldConstraints !== null) {
                    $fieldConstraints[$fieldName] = $setFieldConstraints;
                }
            }

            // handle collections
            if ($fieldType->get('type') === 'collection') {
                // Get constraints for individual types in the collection, and store a mapping
                // from name => constraint in the collection. This mapping is used by a callback validator
                // to validate the individual items of the collection
                $collectionFieldConstraintCombinations = [];
                foreach ($fieldType->get('fields', []) as $collectionFieldName => $collectionFieldType) {
                    $collectionFieldConstraints = $this->getFieldConstraints($collectionFieldType);
                    if ($collectionFieldConstraints !== null) {
                        $collectionFieldConstraintCombinations[$collectionFieldName] = $collectionFieldConstraints;
                    }
                }

                $callback = function ($object, ExecutionContextInterface $context, $constraintLookup): void {
                    if (isset($object['name']) && isset($constraintLookup[$object['name']])) {
                        $itemConstraints = $constraintLookup[$object['name']];
                        // By using ->inContext() the violations are added to the current validation context
                        $context->getValidator()
                            ->inContext($context)
                            ->validate($object['value'], $itemConstraints);
                    }
                };

                // Use optional to prevent failure if the collection has no items
                $collectionItemConstraits = new Assert\Optional(
                    new Assert\All([
                        'constraints' => [
                            new Assert\Callback([
                                'callback' => $callback,
                                'payload' => $collectionFieldConstraintCombinations,
                            ]),
                        ],
                    ])
                );

                // if there is a count constraint set on the collection itself (like on relations)
                $limitConstraintConfig = $fieldType->get('count_constraint', collect([]))->toArray();
                if (\count($limitConstraintConfig) > 0) {
                    // pass contents of 'count_constraint' node to Symfony CountConstraint
                    [$limitConstraint] = $this->loader->parseNodes([['Count' => $limitConstraintConfig]]);
                    $fieldConstraints[$fieldName] = [$limitConstraint, $collectionItemConstraits];
                } else {
                    $fieldConstraints[$fieldName] = $collectionItemConstraits;
                }
            }
        }

        if (count($fieldConstraints) > 0) {
            return new Assert\Collection([
                'fields' => $fieldConstraints,
                'allowExtraFields' => true,
            ]);
        }

        return null;
    }

    private function getConstraints($contentTypeName)
    {
        $contentTypes = $this->config->get('contenttypes');

        /** @var ContentType $contentType */
        $contentType = $contentTypes->get($contentTypeName);

        $fieldConstraints = $this->getFieldConstraints($contentType);

        $relationshipConstraints = [];
        foreach ($contentType->get('relations', []) as $relationType => $relationConfig) {
            $relationConstraintConfig = $relationConfig->get('count_constraint', collect([]))->toArray();
            if (\count($relationConstraintConfig) > 0) {
                // pass contents of 'count_constraint' node to Symfony CountConstraint
                $relationshipConstraints[$relationType] = $this->loader->parseNodes([['Count' => $relationConstraintConfig]]);
            }
        }

        // Note 'fields' is both attribute of collection constraint and a property
        // of the data that is being validated
        return new Assert\Collection([
            'fields' => [
                'fields' => $fieldConstraints,
                'relationship' => new Assert\Collection([
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
