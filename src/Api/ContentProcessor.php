<?php

namespace Bolt\Api;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Content;
use Bolt\Repository\FieldRepository;

/** @implements ProcessorInterface<Content, Content> */
readonly class ContentProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<Content, Content> $persistProcessor
     * @param ProcessorInterface<Content, Content> $removeProcessor
     */
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private Config $config
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        $contentTypes = $this->config->get('contenttypes');

        $data->setDefinitionFromContentTypesConfig($contentTypes);

        foreach ($data->getFields() as $field) {
            $fieldDefinition = FieldType::factory($field->getName(), $data->getDefinition());
            $newField = FieldRepository::factory($fieldDefinition);

            // todo: This works for standalone fields only.
            // See CollectionField.php and SetField.php
            $newField->setName($field->getName());
            $newField->setValue($field->getValue());

            $data->removeField($field);
            $data->addField($newField);
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
