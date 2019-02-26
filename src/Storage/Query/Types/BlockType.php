<?php

namespace Bolt\Storage\Query\Types;

use Bolt\Storage\Query\Parser\ContentFieldParser;
use GraphQL\Type\Definition\ObjectType;

class BlockType extends ObjectType
{
    private $contentFieldParser;

    public function __construct(array $fields, ContentFieldParser $contentFieldParser)
    {
        $this->contentFieldParser = $contentFieldParser;

        $fields = $this->getBlockFields($fields);
        $config = [
            'name' => 'Block_'.md5(time().rand(1000,9999)),
            'fields' => $fields,
        ];
        parent::__construct($config);
    }

    private function getBlockFields(array $fields): array
    {
        foreach ($fields as $fieldName => $field) {
            if (isset($field['fields'])) {

            }
        }
    }
}