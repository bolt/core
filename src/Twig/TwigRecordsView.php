<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Storage\Query\QueryResultset;
use Twig\Markup;

/**
 * Twig Records View, wraps content records before they are passed to Twig
 * so appropriate transformation can occur.
 *
 * @author Ross Riley <riley.ross@gmail.com>
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class TwigRecordsView
{
    /** @var array */
    protected $transformers = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setupDefaults();
    }

    /**
     *  Here we register some transformers that prepare the fields to be passed to
     *  twig. For repeaters and blocks this is recursive.
     */
    protected function setupDefaults(): void
    {
        $this->addTransformer('html', function ($value) {
            return new Markup($value, 'UTF-8');
        });
        $this->addTransformer('text', function ($value) {
            return new Markup($value, 'UTF-8');
        });
        $this->addTransformer('textarea', function ($value) {
            return new Markup($value, 'UTF-8');
        });
    }

    /**
     * This loads the relevant record or records and activates the class.
     *
     * @param Content|QueryResultset $records
     *
     * @return Content|QueryResultset
     */
    public function createView($records)
    {
        if ($records instanceof Content) {
            $this->processSingleRecord($records);
        } elseif ($records instanceof QueryResultset) {
            $this->processRecords($records);
        }

        return $records;
    }

    protected function processSingleRecord(Content $record): void
    {
        $values = $record->getFields();
        foreach ($values as $field) {
            $field->setValue(
                (array) $this->transform(
                    $field->getFlatenValue(),
                    $field->getType(),
                    $field->getValue()
                )
            );
        }
    }

    protected function processRecords($records): void
    {
        foreach ($records as $record) {
            $this->processSingleRecord($record);
        }
    }

    /**
     * Adds a transformer callback to the field type $label.
     */
    public function addTransformer($label, callable $callback): void
    {
        $this->transformers[$label] = $callback;
    }

    /**
     * Checks if a transformer is registered for $label.
     */
    public function hasTransformer($label): bool
    {
        return array_key_exists($label, $this->transformers);
    }

    /**
     * @return array|mixed
     */
    public function getTransformer($label)
    {
        if ($this->hasTransformer($label)) {
            return $this->transformers[$label];
        }
    }

    protected function transform($value, $label, array $fieldData = [])
    {
        if ($this->hasTransformer($label)) {
            return $this->transformers[$label]($value, $fieldData);
        }

        return $value;
    }
}
