<?php

namespace Bolt\Storage\Query\Types;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use GraphQL\Type\Definition\IDType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\IntType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\StringType;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    private $configuration;

    public function __construct(Config $configuration)
    {
        $this->configuration = $configuration;

        $config = [
            'name' => 'Query',
            'fields' => $this->generateQueryFields(),
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($val, $args, $context, $info);
            }
        ];

        parent::__construct($config);
    }

    public function __call($name, $arguments)
    {
        dump($name, $arguments);die;
    }

    private function generateQueryFields(): array
    {
        $contentFields = $this->contentFields();
        $queryContentFields = [];
        foreach ($contentFields as $contentType => $fields) {
           $queryContentFields[$contentType] = [
               'type' => new ContentType($contentType, $fields),
               'description' => 'Represents list of '.$contentType,
               'args' => [
                   'first' => [
                       'type' => Type::id(),
                       'description' => 'Fetch record of content by id',
                   ],
                   'limit' => [
                       'type' => Type::int(),
                       'description' => 'Fetch records of content',
                       'defaultValue' => 10,
                   ],
                   'filter' => [
                       'type' => Type::nonNull(new InputObjectType([
                           'name' => 'ContentFilterInput_'.md5(time().rand(1000,9999)),
                           'fields' => $this->getFilterFields($contentType),
                       ])),
                       'description' => 'Filter records',
                       'defaultValue' => [
                           'title_not' => '',
                       ]
                   ]
               ]
           ];
        }

        return array_merge($queryContentFields, [
            'hello' => Type::string(),
            ]);
    }

    private function contentFields(): array
    {
        $contentTypes = $this->configuration->get('contenttypes');
        $contentTypesFields = [];
        /** @var DeepCollection $contentTypeConfiguration */
        foreach ($contentTypes as $contentType => $contentTypeConfiguration) {
            $contentTypesFields[$contentType] = $this->parseContentTypeFields($contentType, $contentTypeConfiguration->get('fields'));
        }

        return $contentTypesFields;
    }

    private function parseContentTypeFields(string $contentType, DeepCollection $fields): array
    {
        $parsedFields = [];
        foreach ($fields as $fieldName => $fieldConfiguration) {
            $parsedFields[$fieldName] = $this->getTypeForField($contentType, $fieldConfiguration);
            $parsedFields['contentType'] = $this->getTypeForField($contentType, new DeepCollection(['type' => 'string']));
        }

        return $this->prepareConditionalFields($parsedFields);
    }

    private function getTypeForField(string $contentType, DeepCollection $fieldConfiguration): Type
    {
        switch ($fieldConfiguration['type']) {
            case 'text':
            case 'slug':
            case 'textarea':
            case 'html':
            case 'templateselect':
            case 'file':
            case 'image':
            case 'video':
            case 'select':
            case 'filelist':
            case 'imagelist':
            case 'embed':
            case 'geolocation':
            case 'markdown':
                return Type::string();
                break;
            case 'checkbox':
                return Type::int();
                break;
            case 'number':
                if ($fieldConfiguration['mode'] === 'integer'){
                    return Type::int();
                } else {
                    return Type::float();
                }
                break;
            case 'date':
                return new DateType();
                break;
            case 'repeater':
                return new RepeaterType($this->parseContentTypeFields($contentType, $fieldConfiguration['fields']));
                break;
        }

        return Type::string();
    }

    private function prepareConditionalFields(array $contentFields): array
    {
        $conditionalFields = [];
        foreach ($contentFields as $field => $type) {
            switch (true) {
                case $type instanceof StringType:
                    $conditionalFields += [
                        $field.'_contains' => $type,
                        $field.'_not_contains' => $type,
                    ];
                    break;
                case $type instanceof IntType:
                case $type instanceof IDType:
                    $conditionalFields += [
                        $field.'_lt' => $type,
                        $field.'_lte' => $type,
                        $field.'_gt' => $type,
                        $field.'_gte' => $type,
                    ];
                    break;
                case $type instanceof DateType:
                    $conditionalFields += [
                        $field.'_contains' => $type,
                        $field.'_not_contains' => $type,
                        $field.'_lt' => $type,
                        $field.'_lte' => $type,
                        $field.'_gt' => $type,
                        $field.'_gte' => $type,
                    ];
                    break;
            }
            $conditionalFields += [
                $field.'_in' => Type::listOf($type),
                $field.'_not_in' => Type::listOf($type),
                $field.'_not' => $type,
            ];
        }

        return $contentFields + $conditionalFields;
    }

    private function getFilterFields(string $contentType, bool $isFirst = true): array
    {
        $filterFields = [
            'OR' => [
                'type' => new ListOfType(Type::nonNull(
                    new InputObjectType([
                        'name' => 'OR_filter_'.md5(time().rand(1000,9999)),
                        'fields' => $isFirst ? array_merge(
                            $this->contentFields()[$contentType], $this->getFilterFields($contentType,false)
                        ) : $this->contentFields()[$contentType]
                    ])
                )),
            ],
            'AND' => [
                'type' => new ListOfType(Type::nonNull(
                    new InputObjectType([
                        'name' => 'AND_filter_'.md5(time().rand(1000,9999)),
                        'fields' => $isFirst ? array_merge(
                            $this->contentFields()[$contentType], $this->getFilterFields($contentType,false)
                        ) : $this->contentFields()[$contentType]
                    ])
                )),
            ],
        ];

        return array_merge($filterFields, $this->contentFields()[$contentType]);
    }

    private function hello(): string
    {
        return 'This message will be shown if welcome query works!';
    }
}