<?php

namespace Bolt\Api;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Metadata\GraphQl\Operation;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Content;
use Bolt\Repository\FieldRepository;

readonly class ContentDataPersister
{
    public function __construct(
        private PersistProcessor $persistProcessor,
        private RemoveProcessor $removeProcessor,
        private Config           $config
    ) {
    }

    public function persist($data): void
    {
        if ($data instanceof Content) {
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
        }

        $this->persistProcessor->process($data, new Operation());
    }

    public function remove($data): void
    {
        $this->removeProcessor->process($data, new Operation());
    }

    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = [])
    {
        // TODO: Implement process() method.
    }
}
