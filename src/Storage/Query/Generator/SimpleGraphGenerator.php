<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Generator;

use Bolt\Configuration\Config;
use Bolt\Storage\Query\Definition\ContentFieldsDefinition;
use Bolt\Storage\Query\Definition\FieldDefinition;

class SimpleGraphGenerator
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function generate(string $query): string
    {
        $this->validateQuery($query);

        return $this->doGenerate($query);
    }

    private function validateQuery(string $query): void
    {
        $existingContentTypes = array_keys($this->config->get('contenttypes')->toArray());

        if (mb_strpos($query, '/')) {
            [$contentType, $searchValue] = explode('/', $query);

            if ((int) $searchValue && $this->conditionValid($searchValue) === false) {
                throw new \Exception('Search value is wrong');
            }
        } else {
            $contentType = trim(trim($query), '/');
        }

        if (in_array($contentType, $existingContentTypes, true) === false) {
            throw new \Exception(sprintf('Content type %s is not defined', $contentType));
        }
    }

    private function conditionValid(string $condition): bool
    {
        if (preg_match('#[^a-zA-Z0-9_\-]#', $condition)) {
            return false;
        }

        return true;
    }

    private function doGenerate(string $query): string
    {
        if (mb_strpos($query, '/')) {
            [$contentType, $condition] = explode('/', $query);
        } else {
            $contentType = trim(trim($query), '/');
        }
        $fields = $this->config->get('contenttypes')
            ->get($contentType)
            ->get('fields')
            ->toArray();

        $fields = array_merge($fields, ContentFieldsDefinition::getMainContentFields(true));

        $fields = $this->prepareSubFieldType($fields);

        $joinedFields = implode(' ', $fields);

        if (isset($condition)) {
            $conditionType = (int) $condition ? 'id' : 'slug';
            return 'query {' . $contentType . ' (filter: {'.$conditionType.': "' . $condition . '"}) {' . $joinedFields . '}}';
        }

        return 'query {' . $contentType . ' {' . $joinedFields . '}}';
    }

    private function prepareSubFieldType(array $fields): array
    {
        $stringFields = [];
        foreach ($fields as $key => $field) {
            if (in_array($field['type'], array_keys(FieldDefinition::SUB_FIELDS), true)) {
                $stringFields[$key] = $key.' { '.implode(' ', FieldDefinition::SUB_FIELDS[$field['type']]).'}';
                continue;
            } elseif (in_array($field['type'], FieldDefinition::CUSTOM_FIELDS, true)) {
                $stringFields[$key] = $this->getCustomSubFieldsForQuery($key, $field['fields']);
            } else {
                $stringFields[$key] = $key;
            }
        }

        return $stringFields;
    }

    private function getCustomSubFieldsForQuery(string $fieldName, array $fields): string
    {
        $stringFields = [];
        foreach ($fields as $key => $field) {
            if (isset($field['fields'])) {
                $stringFields[$key] = $this->getCustomSubFieldsForQuery($key, $field['fields']);
            }
        }

        $stringFields = $this->prepareSubFieldType($fields);

        return $fieldName.' { '.implode(' ', $stringFields).'}';
    }
}
