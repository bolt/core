<?php

namespace Bolt\Storage\Query\Generator;

use Bolt\Configuration\Config;

class SimpleGraphGenerator
{
    private $config;

    private $subFieldTypes = [
        'image' => [
            'filename', 'alt', 'path',
        ],
    ];

    private $customSubFieldTypes = [
        'repeater', 'block',
    ];

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

        if (strpos($query, '/')) {
            [$contentType, $searchValue] = explode('/', $query);

            if ($this->conditionValid($searchValue) === false) {
                throw new \Exception('Search value is wrong');
            }
        } else {
            $contentType = trim(trim($query),'/');
        }

        if (in_array($contentType, $existingContentTypes) === false) {
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
        [$contentType, $condition] = explode('/', $query);

        $fields = $this->config->get('contenttypes')
                ->get($contentType)
                ->get('fields')
                ->toArray();

        $fields = $this->prepareSubFieldType($fields);

        $joinedFields = join(' ', $fields);

        $query = 'query {'.$contentType.' (filter: {slug_contains: "'.$condition.'"}) {'.$joinedFields.'}}';

        return $query;
    }

    private function prepareSubFieldType(array $fields): array
    {
        $stringFields = [];
        foreach ($fields as $key => $field) {
            if (in_array($field['type'], array_keys($this->subFieldTypes))) {
                $stringFields[$key] = $key.' { '.join(' ', $this->subFieldTypes[$field['type']]).'}';
                continue;
            } elseif (in_array($field['type'] , $this->customSubFieldTypes)) {
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

        return $fieldName.' { '.join(' ', $stringFields).'}';
    }
}