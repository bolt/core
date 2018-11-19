<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Tightenco\Collect\Support\Collection;

/**
 * A class for choosing whichever template should be used.
 */
class TemplateChooser
{
    /** @var Config */
    private $config;

    /**
     * Constructor.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Choose a template for the homepage.
     */
    public function homepage(?Content $content = null): Collection
    {
        $templates = collect([]);

        // First candidate: Theme-specific config.yml file.
        if ($template = $this->config->get('theme/homepage_template')) {
            $templates->push($template);
        }

        // Second candidate: Global config.yml file.
        if ($template = $this->config->get('general/homepage_template')) {
            $templates->push($template);
        }

        if (empty($content)) {
            // Fallback if no content: index.twig
            $templates->push('index.html.twig');
        } elseif (is_array($content)) {
            // Fallback with multiple content: use listing() to choose template
            $first = reset($content);
            $templates->merge($this->listing($first->contenttype));
        } else {
            // Fallback with single content: use record() to choose template
            $templates->merge($templates, $this->record($content));
        }

        return $templates->unique();
    }

    /**
     * Choose a template for a single record page, e.g.:
     * - '/page/about'
     * - '/entry/lorum-ipsum'.
     */
    public function record(Content $record, ?array $data = null): Collection
    {
        $templates = collect([]);

        // First candidate: A legacy Content record has a templateselect field, and it's set.
        if (isset($record->contenttype['fields'])) {
            foreach ($record->contenttype['fields'] as $name => $field) {
                if ($field['type'] === 'templateselect' && ! empty($record->values[$name])) {
                    $templates->push($record->values[$name]);
                }
            }
        }

        // Second candidate: An entity has a templateselect field, and it's set.
        if (isset($record->contenttype['fields'])) {
            foreach ($record->contenttype['fields'] as $name => $field) {
                if ($field['type'] === 'templateselect' && ! empty($record[$name])) {
                    $templates->push($record[$name]);
                }

                if ($field['type'] === 'templateselect' && $data !== null && ! empty($data[$name])) {
                    $templates->push($data[$name]);
                }
            }
        }

        // Third candidate: defined specifically in the contenttype.
        if (isset($record->contenttype['record_template'])) {
            $templates->push($record->contenttype['record_template']);
        }

        // Fourth candidate: a template with the same filename as the name of
        // the contenttype.
        if (isset($record->contenttype['singular_slug'])) {
            $templates->push($record->contenttype['singular_slug'] . '.html.twig');
        }

        // Fifth candidate: Theme-specific config.yml file.
        if ($template = $this->config->get('theme/record_template')) {
            $templates->push($template);
        }

        // Sixth candidate: global config.yml
        $templates->push($this->config->get('general/record_template'));

        // Seventh candidate: fallback to 'record.html.twig'
        $templates->push('record.html.twig');

        return $templates->unique();
    }

    /**
     * Select a template for listing pages.
     */
    public function listing(?Collection $contenttype = null): Collection
    {
        $templates = collect([]);

        // First candidate: defined specifically in the contenttype.
        if (! empty($contenttype['listing_template'])) {
            $templates->push($contenttype['listing_template']);
        }

        // Second candidate: a template with the same filename as the name of
        // the contenttype.
        if (! empty($contenttype['listing_template'])) {
            $templates->push($contenttype['slug'] . '.html.twig');
        }

        // Third candidate: Theme-specific config.yml file.
        if ($template = $this->config->get('theme/listing_template')) {
            $templates->push($template);
        }

        // Fourth candidate: Global config.yml
        $templates->push($this->config->get('general/listing_template'));

        // Fifth candidate: fallback to 'listing.html.twig'
        $templates->push('listing.html.twig');

        return $templates->unique();
    }

    /**
     * Select a template for taxonomy.
     */
    public function taxonomy(string $taxonomyslug): Collection
    {
        $templates = collect([]);

        // First candidate: defined specifically in the taxonomy
        if ($template = $this->config->get('taxonomy/' . $taxonomyslug . '/listing_template')) {
            $templates->push($template);
        }

        // Second candidate: Theme-specific config.yml file.
        if ($template = $this->config->get('theme/listing_template')) {
            $templates->push($template);
        }

        // Third candidate: Global config.yml
        $templates->push($this->config->get('general/listing_template'));

        return $templates->unique();
    }

    /**
     * Select a search template.
     */
    public function search(): Collection
    {
        $templates = collect([]);

        // First candidate: specific search setting in global config.
        if ($template = $this->config->get('theme/search_results_template')) {
            $templates->push($template);
        }

        // Second candidate: specific search setting in global config.
        if ($template = $this->config->get('general/search_results_template')) {
            $templates->push($template);
        }

        // Third candidate: listing config setting.
        $templates->push($this->config->get('general/listing_template'));

        return $templates->unique();
    }

    /**
     * Select a template to use for the "maintenance" page.
     */
    public function maintenance(): Collection
    {
        $templates = collect([]);

        // First candidate: Theme-specific config.
        if ($template = $this->config->get('theme/maintenance_template')) {
            $templates->push($template);
        }

        // Second candidate: global config.
        $templates->push($this->config->get('general/maintenance_template'));

        return $templates->unique();
    }
}
