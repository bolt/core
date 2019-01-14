<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Utils\Str;
use Tightenco\Collect\Support\Collection;

class TaxonomyParser extends BaseParser
{
    /**
     * Read and parse the taxonomy.yml configuration file.
     */
    public function parse(): Collection
    {
        $taxonomies = $this->parseConfigYaml('taxonomy.yaml');

        foreach ($taxonomies as $key => $taxonomy) {
            if (! isset($taxonomy['name'])) {
                $taxonomy['name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['slug'])));
            }
            if (! isset($taxonomy['singular_name'])) {
                if (isset($taxonomy['singular_slug'])) {
                    $taxonomy['singular_name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['singular_slug'])));
                } else {
                    $taxonomy['singular_name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['slug'])));
                }
            }
            if (! isset($taxonomy['slug'])) {
                $taxonomy['slug'] = Str::slug($taxonomy['name']);
            }
            if (! isset($taxonomy['singular_slug'])) {
                $taxonomy['singular_slug'] = Str::slug($taxonomy['singular_name']);
            }
            if (! isset($taxonomy['has_sortorder'])) {
                $taxonomy['has_sortorder'] = false;
            }
            if (! isset($taxonomy['allow_spaces'])) {
                $taxonomy['allow_spaces'] = false;
            }
            if (! isset($taxonomy['allow_empty'])) {
                $taxonomy['allow_empty'] = true;
            }
            if ($taxonomy['behaves_like'] === 'grouping') {
                $taxonomy['multiple'] = false;
            } elseif ($taxonomy['behaves_like'] === 'tags' || (isset($taxonomy['multiple']) && $taxonomy['multiple'])) {
                $taxonomy['multiple'] = true;
            } else {
                $taxonomy['multiple'] = false;
            }

            // Make sure the options are $key => $value pairs, and not have implied integers for keys.
            if (! empty($taxonomy['options']) && is_array($taxonomy['options'])) {
                $options = [];
                foreach ($taxonomy['options'] as $optionKey => $optionValue) {
                    if (is_numeric($optionKey)) {
                        $optionKey = $optionValue;
                        $optionValue = Str::humanize($optionValue);
                    }
                    $optionKey = Str::slug($optionKey);
                    $options[$optionKey] = $optionValue;
                }
                $taxonomy['options'] = $options;
            } else {
                $taxonomy['options'] = [];
            }

            if (! isset($taxonomy['behaves_like'])) {
                $taxonomy['behaves_like'] = 'tags';
            }
            // If taxonomy is like tags, set 'tagcloud' to true by default.
            if (($taxonomy['behaves_like'] === 'tags') && (! isset($taxonomy['tagcloud']))) {
                $taxonomy['tagcloud'] = true;
            } else {
                $taxonomy += ['tagcloud' => false];
            }

            $taxonomies[$key] = $taxonomy;
        }

        return collect($taxonomies);
    }
}
