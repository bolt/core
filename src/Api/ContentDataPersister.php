<?php

namespace Bolt\Api;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Content;
use Bolt\Repository\FieldRepository;

class ContentDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var ContextAwareDataPersisterInterface */
    private $decorated;

    /** @var Config */
    private $config;

    public function __construct(ContextAwareDataPersisterInterface $decorated, Config $config)
    {
        $this->decorated = $decorated;
        $this->config = $config;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = []): void
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

        $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = []): void
    {
        $this->decorated->remove($data, $context);
    }
}
