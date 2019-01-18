<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Common\Arr;
use Bolt\Enum\Statuses;
use Bolt\Utils\Str;
use Exception;
use Tightenco\Collect\Support\Collection;

class ContentTypesParser extends BaseParser
{
    /**
     * @var string[]
     */
    private $acceptFileTypes = [];

    public function __construct(array $acceptFileTypes)
    {
        $this->acceptFileTypes = $acceptFileTypes;
        parent::__construct();
    }

    /**
     * Read and parse the contenttypes.yml configuration file.
     *
     * @throws Exception
     */
    public function parse(): Collection
    {
        $contentTypes = new Collection();
        $tempContentTypes = $this->parseConfigYaml('contenttypes.yaml');
        foreach ($tempContentTypes as $key => $contentType) {
            $contentType = $this->parseContentType($key, $contentType);
            $contentTypes[$key] = $contentType;
        }

        return $contentTypes;
    }

    /**
     * Parse a single Content Type configuration array.
     *
     * @param string $key
     * @param array  $contentType
     *
     * @throws \Exception
     */
    protected function parseContentType($key, $contentType): array
    {
        // If the slug isn't set, and the 'key' isn't numeric, use that as the slug.
        if (! isset($contentType['slug']) && ! is_numeric($key)) {
            $contentType['slug'] = Str::slug($key);
        }

        // If neither 'name' nor 'slug' is set, we need to warn the user. Same goes for when
        // neither 'singular_name' nor 'singular_slug' is set.
        if (! isset($contentType['name']) && ! isset($contentType['slug'])) {
            $error = sprintf("In content type <code>%s</code>, neither 'name' nor 'slug' is set. Please edit <code>contenttypes.yml</code>, and correct this.", $key);
            throw new Exception($error);
        }
        if (! isset($contentType['singular_name']) && ! isset($contentType['singular_slug'])) {
            $error = sprintf("In content type <code>%s</code>, neither 'singular_name' nor 'singular_slug' is set. Please edit <code>contenttypes.yml</code>, and correct this.", $key);
            throw new Exception($error);
        }

        // Content types without fields make no sense.
        if (! isset($contentType['fields'])) {
            $error = sprintf("In content type <code>%s</code>, no 'fields' are set. Please edit <code>contenttypes.yml</code>, and correct this.", $key);
            throw new Exception($error);
        }

        if (! isset($contentType['slug'])) {
            $contentType['slug'] = Str::slug($contentType['name']);
        }
        if (! isset($contentType['name'])) {
            $contentType['name'] = ucwords(preg_replace('/[^a-z0-9]/i', ' ', $contentType['slug']));
        }
        if (! isset($contentType['singular_slug'])) {
            $contentType['singular_slug'] = Str::slug($contentType['singular_name']);
        }
        if (! isset($contentType['singular_name'])) {
            $contentType['singular_name'] = ucwords(preg_replace('/[^a-z0-9]/i', ' ', $contentType['singular_slug']));
        }
        if (! isset($contentType['show_on_dashboard'])) {
            $contentType['show_on_dashboard'] = true;
        }
        if (! isset($contentType['show_in_menu'])) {
            $contentType['show_in_menu'] = true;
        }
        if (! isset($contentType['sort'])) {
            $contentType['sort'] = false;
        }
        if (! isset($contentType['default_status'])) {
            $contentType['default_status'] = Statuses::PUBLISHED;
        }
        if (! isset($contentType['viewless'])) {
            $contentType['viewless'] = false;
        }
        if (! isset($contentType['icon_one'])) {
            $contentType['icon_one'] = 'fa-file';
        } else {
            $contentType['icon_one'] = str_replace('fa:', 'fa-', $contentType['icon_one']);
        }
        if (! isset($contentType['icon_many'])) {
            $contentType['icon_many'] = 'fa-copy';
        } else {
            $contentType['icon_many'] = str_replace('fa:', 'fa-', $contentType['icon_many']);
        }

        // Allow explicit setting of a Content Type's table name suffix. We default to slug if not present.
        if (isset($contentType['tablename'])) {
            $contentType['tablename'] = Str::slug($contentType['tablename'], '_');
        } else {
            $contentType['tablename'] = Str::slug($contentType['slug'], '_');
        }

        if (! isset($contentType['allow_numeric_slugs'])) {
            $contentType['allow_numeric_slugs'] = false;
        }
        if (! isset($contentType['singleton'])) {
            $contentType['singleton'] = false;
        }

        if (! isset($contentType['locales'])) {
            $contentType['locales'] = [];
        } elseif (is_string($contentType['locales'])) {
            $contentType['locales'] = (array) $contentType['locales'];
        }

        [$fields, $groups] = $this->parseFieldsAndGroups($contentType['fields']);
        $contentType['fields'] = $fields;
        $contentType['groups'] = $groups;

        // Make sure taxonomy is an array.
        if (isset($contentType['taxonomy'])) {
            $contentType['taxonomy'] = (array) $contentType['taxonomy'];
        } else {
            $contentType['taxonomy'] = [];
        }

        // when adding relations, make sure they're added by their slug. Not their 'name' or 'singular name'.
        if (! empty($contentType['relations']) && is_array($contentType['relations'])) {
            foreach (array_keys($contentType['relations']) as $relkey) {
                if ($relkey !== Str::slug($relkey)) {
                    $contentType['relations'][Str::slug($relkey)] = $contentType['relations'][$relkey];
                    unset($contentType['relations'][$relkey]);
                }
            }
        } else {
            $contentType['relations'] = [];
        }

        if (! empty($contentType['relations']) || ! empty($contentType['taxonomy'])) {
            $contentType['groups'][] = 'Relations';
        }

        return $contentType;
    }

    /**
     * Parse a Content Type's field and determine the grouping.
     *
     * @throws Exception
     */
    protected function parseFieldsAndGroups(array $fields): array
    {
        $currentGroup = 'ungrouped';
        $groups = [];
        $hasGroups = false;

        foreach ($fields as $key => $field) {
            unset($fields[$key]);
            $key = str_replace('-', '_', mb_strtolower(Str::makeSafe($key, true)));
            if (! isset($field['type']) || empty($field['type'])) {
                $error = sprintf('Field "%s" has no "type" set.', $key);

                throw new \Exception($error);
            }

            // If field is a "file" type, make sure the 'extensions' are set, and it's an array.
            if ($field['type'] === 'file' || $field['type'] === 'filelist') {
                if (empty($field['extensions'])) {
                    $field['extensions'] = $this->acceptFileTypes;
                }

                $field['extensions'] = (array) $field['extensions'];
            }

            // If field is an "image" type, make sure the 'extensions' are set, and it's an array.
            if ($field['type'] === 'image' || $field['type'] === 'imagelist') {
                if (empty($field['extensions'])) {
                    $field['extensions'] = collect(['gif', 'jpg', 'jpeg', 'png', 'svg'])
                        ->intersect($this->acceptFileTypes);
                }

                $field['extensions'] = (array) $field['extensions'];
            }

            // Make indexed arrays into associative for select fields
            // e.g.: [ 'yes', 'no' ] => { 'yes': 'yes', 'no': 'no' }
            if ($field['type'] === 'select' && isset($field['values']) && Arr::isIndexed($field['values'])) {
                $field['values'] = array_combine($field['values'], $field['values']);
            }

            if (! empty($field['group'])) {
                $hasGroups = true;
            }

            // Make sure we have these keys and every field has a group set.
            $field = array_replace(
                [
                    'class' => '',
                    'default' => '',
                    'group' => $currentGroup,
                    'label' => '',
                    'variant' => '',
                    'localize' => false,
                ],
                $field
            );

            // Collect group data for rendering.
            // Make sure that once you started with group all following have that group, too.
            $currentGroup = $field['group'];
            $groups[$currentGroup] = 1;

            $fields[$key] = $field;

            // Repeating fields checks
            if ($field['type'] === 'repeater') {
                $fields[$key] = $this->parseFieldRepeaters($fields[$key]);
                if ($fields[$key] === null) {
                    unset($fields[$key]);
                }
            }
        }

        // Make sure the 'uses' of the slug is an array.
        if (isset($fields['slug']) && isset($fields['slug']['uses'])) {
            $fields['slug']['uses'] = (array) $fields['slug']['uses'];
        }

        return [$fields, $hasGroups ? array_keys($groups) : []];
    }

    /**
     * Basic validation of repeater fields.
     */
    private function parseFieldRepeaters(array $repeater): array
    {
        $blacklist = ['repeater', 'slug', 'templatefield'];

        if (! isset($repeater['fields']) || ! is_array($repeater['fields'])) {
            return [];
        }

        foreach ($repeater['fields'] as $repeaterKey => $repeaterField) {
            if (! isset($repeaterField['type']) || in_array($repeaterField['type'], $blacklist, true)) {
                unset($repeater['fields'][$repeaterKey]);
            }
        }

        return $repeater;
    }
}
