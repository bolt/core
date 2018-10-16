<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Helpers\Str;
use Cocur\Slugify\Slugify;
use Tightenco\Collect\Support\Collection;

class TaxonomyParser extends BaseParser
{
    /**
     * Read and parse the taxonomy.yml configuration file.
     *
     * @return Collection
     */
    public function parse(): Collection
    {
        $taxonomies = $this->parseConfigYaml('taxonomy.yml');

        $slugify = Slugify::create();

        foreach ($taxonomies as $key => $taxonomy) {
            if (!isset($taxonomy['name'])) {
                $taxonomy['name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['slug'])));
            }
            if (!isset($taxonomy['singular_name'])) {
                if (isset($taxonomy['singular_slug'])) {
                    $taxonomy['singular_name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['singular_slug'])));
                } else {
                    $taxonomy['singular_name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['slug'])));
                }
            }
            if (!isset($taxonomy['slug'])) {
                $taxonomy['slug'] = $slugify->slugify($taxonomy['name']);
            }
            if (!isset($taxonomy['singular_slug'])) {
                $taxonomy['singular_slug'] = $slugify->slugify($taxonomy['singular_name']);
            }
            if (!isset($taxonomy['has_sortorder'])) {
                $taxonomy['has_sortorder'] = false;
            }
            if (!isset($taxonomy['allow_spaces'])) {
                $taxonomy['allow_spaces'] = false;
            }

            // Make sure the options are $key => $value pairs, and not have implied integers for keys.
            if (!empty($taxonomy['options']) && is_array($taxonomy['options'])) {
                $options = [];
                foreach ($taxonomy['options'] as $optionKey => $optionValue) {
                    if (is_numeric($optionKey)) {
                        $optionKey = $optionValue;
                    }
                    $optionKey = $slugify->slugify($optionKey);
                    $options[$optionKey] = $optionValue;
                }
                $taxonomy['options'] = $options;
            }

            if (!isset($taxonomy['behaves_like'])) {
                $taxonomy['behaves_like'] = 'tags';
            }
            // If taxonomy is like tags, set 'tagcloud' to true by default.
            if (($taxonomy['behaves_like'] === 'tags') && (!isset($taxonomy['tagcloud']))) {
                $taxonomy['tagcloud'] = true;
            } else {
                $taxonomy += ['tagcloud' => false];
            }

            $taxonomies[$key] = $taxonomy;
        }

        return collect($taxonomies);
    }
}
