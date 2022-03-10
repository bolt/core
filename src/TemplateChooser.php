<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Twig\ContentExtension;
use Tightenco\Collect\Support\Collection;

class TemplateChooser
{
    /** @var Config */
    private $config;

    /** @var ContentExtension */
    private $contentExtension;

    public function __construct(Config $config, ContentExtension $contentExtension)
    {
        $this->config = $config;
        $this->contentExtension = $contentExtension;
    }

    public function forHomepage(?Content $content = null): array
    {
        $templates = new Collection();

        // First candidate: Theme-specific config.yml file.
        $templates->push($this->config->get('theme/homepage_template'));

        // Second candidate: Global config.yml file.
        $templates->push($this->config->get('general/homepage_template'));

        if (empty($content)) {
            // Fallback if no content: index.twig
            $templates->push('index.html.twig')->push('index.twig');
        } else {
            // Fallback with single content: use record() to choose template
            $templates = $templates->merge($this->forRecord($content));
        }

        return $templates->unique()->filter()->toArray();
    }

    /**
     * Choose a template for a single record page, e.g.:
     * - '/page/about'
     * - '/entry/lorum-ipsum'.
     */
    public function forRecord(Content $record): array
    {
        $templates = new Collection();
        $definition = $record->getDefinition();

        // First candidate: Content record is the homepage
        if ($this->contentExtension->isHomepage($record)) {
            $templates = collect($this->forHomepage());
        }

        // Second candidate: Content record has a templateselect field, and it's set.
        foreach ($definition->get('fields') as $name => $field) {
            if ($field['type'] === 'templateselect' && $record->hasField($name)) {
                $templates->push((string) $record->getField($name));
            }
        }

        // Third candidate: defined specifically in the content type.
        if ($definition->has('record_template')) {
            $templates->push($definition->get('record_template'));
        }

        // Fourth candidate: a template with the same filename as the name of
        // the content type.
        $templates->push($definition->get('singular_slug') . '.html.twig');
        $templates->push($definition->get('singular_slug') . '.twig');

        // Fifth candidate: Theme-specific config.yml file.
        $templates->push($this->config->get('theme/record_template'));

        // Sixth candidate: global config.yml
        $templates->push($this->config->get('general/record_template'));

        // Seventh candidate: fallback to 'record.html.twig'
        $templates->push('record.html.twig');

        return $templates->unique()->filter()->toArray();
    }

    public function forListing(ContentType $contentType): array
    {
        $templates = new Collection();

        // First candidate: Content record is the homepage
        if ($this->contentExtension->isHomepageListing($contentType)) {
            $templates = collect($this->forHomepage());
        }

        // Second candidate: defined specifically in the content type.
        if (! empty($contentType['listing_template'])) {
            $templates->push($contentType['listing_template']);
        }

        // Third candidate: a template with the same filename as the name of
        // the content type.
        $templates->push($contentType->getSlug() . '.html.twig');
        $templates->push($contentType->getSlug() . '.twig');

        // Fourth candidate: Theme-specific config.yml file.
        $templates->push($this->config->get('theme/listing_template'));

        // Fifth candidate: Global config.yml
        $templates->push($this->config->get('general/listing_template'));

        // Sixth candidate: fallback to 'listing.html.twig'
        $templates->push('listing.html.twig');

        return $templates->unique()->filter()->toArray();
    }

    public function forTaxonomy(Collection $taxonomy): array
    {
        $templates = new Collection();

        // First candidate: defined specifically in the taxonomy
        $templates->push($taxonomy->get('listing_template', null));

        // Second candidate: Theme-specific config.yml file.
        $templates->push($this->config->get('theme/listing_template'));

        // Third candidate: Global config.yml
        $templates->push($this->config->get('general/listing_template'));

        return $templates->unique()->filter()->toArray();
    }

    public function forSearch(): array
    {
        $templates = new Collection();

        // First candidate: specific search setting in global config.
        $templates->push($this->config->get('theme/search_results_template'));

        // Second candidate: specific search setting in global config.
        $templates->push($this->config->get('general/search_results_template'));

        // Third candidate: listing config setting.
        $templates->push($this->config->get('general/listing_template'));

        return $templates->unique()->filter()->toArray();
    }

    public function forMaintenance(): array
    {
        $templates = new Collection();

        // First candidate: Theme-specific config.
        $templates->push($this->config->get('theme/maintenance_template'));

        // Second candidate: global config.
        $templates->push($this->config->get('general/maintenance_template'));

        return $templates->unique()->filter()->toArray();
    }

    public function forLogin(): array
    {
        $templates = new Collection();

        // First candidate: Theme-specific config.
        $templates->push($this->config->get('theme/login_template'));

        // Second candidate: global config.
        $templates->push($this->config->get('general/login_template'));

        // Third candidate: default value.
        $templates->push('@bolt/security/login.html.twig');

        return $templates->unique()->filter()->toArray();
    }

    public function forResetPasswordCheckEmail(): array
    {
        $templates = new Collection();

        // First candidate: Theme-specific config.
        $templates->push($this->config->get('theme/reset_password_settings/check_email_template'));

        // Second candidate: global config.
        $templates->push($this->config->get('general/reset_password_settings/check_email_template'));

        // Third candidate: default value.
        $templates->push('@bolt/reset_password/check_email.html.twig');

        return $templates->unique()->filter()->toArray();
    }

    public function forResetPasswordRequest(): array
    {
        $templates = new Collection();

        // First candidate: Theme-specific config.
        $templates->push($this->config->get('theme/reset_password_settings/request_template'));

        // Second candidate: global config.
        $templates->push($this->config->get('general/reset_password_settings/request_template'));

        // Third candidate: default value.
        $templates->push('@bolt/reset_password/request.html.twig');

        return $templates->unique()->filter()->toArray();
    }

    public function forResetPasswordReset(): array
    {
        $templates = new Collection();

        // First candidate: Theme-specific config.
        $templates->push($this->config->get('theme/reset_password_settings/reset_template'));

        // Second candidate: global config.
        $templates->push($this->config->get('general/reset_password_settings/reset_template'));

        // Third candidate: default value.
        $templates->push('@bolt/reset_password/reset.html.twig');

        return $templates->unique()->filter()->toArray();
    }
}
