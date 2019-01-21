<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Common\Json;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Repository\TaxonomyRepository;
use Carbon\Carbon;

class ContentFromFormdata
{
    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    public function __construct()
    {
    }

    public function update(Content $content, array $post): Content
    {
        $locale = $this->getPostedLocale($post);

        $content->setStatus(Json::findScalar($post['status']));
        $content->setPublishedAt(new Carbon($post['publishedAt']));
        $content->setDepublishedAt(new Carbon($post['depublishedAt']));

        foreach ($post['fields'] as $key => $postfield) {
            $this->updateFieldFromPost($key, $postfield, $content, $locale);
        }

        if (isset($post['taxonomy'])) {
            foreach ($post['taxonomy'] as $key => $taxonomy) {
                $this->updateTaxonomyFromPost($key, $taxonomy, $content);
            }
        }

        return $content;
    }

    private function updateFieldFromPost(string $key, $postfield, Content $content, string $locale): void
    {
        if ($content->hasLocalizedField($key, $locale)) {
            $field = $content->getLocalizedField($key, $locale);
        } else {
            $fields = collect($content->getDefinition()->get('fields'));
            $field = Field::factory($fields->get($key), $key);
            $field->setName($key);
            $content->addField($field);
        }

        // If the value is an array that contains a string of JSON, parse it
        if (is_iterable($postfield) && Json::test(current($postfield))) {
            $postfield = Json::findArray($postfield);
        }

        $field->setValue((array) $postfield);

        if ($field->getDefinition()->get('localize')) {
            $field->setLocale($locale);
        } else {
            $field->setLocale('');
        }
    }

    private function updateTaxonomyFromPost(string $key, $taxonomy, Content $content): void
    {
        $taxonomy = collect(Json::findArray($taxonomy))->filter();

        // Remove old ones
        foreach ($content->getTaxonomies($key) as $current) {
            $content->removeTaxonomy($current);
        }

        // Then (re-) add selected ones
        foreach ($taxonomy as $slug) {
            $taxonomy = $this->taxonomyRepository->findOneBy([
                'type' => $key,
                'slug' => $slug,
            ]);

            if (! $taxonomy) {
                $taxonomy = Taxonomy::factory($key, $slug);
            }

            $content->addTaxonomy($taxonomy);
        }
    }

    private function getPostedLocale(array $post): string
    {
        return $post['_edit_locale'] ?: '';
    }
}
