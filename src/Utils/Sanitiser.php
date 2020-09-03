<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Configuration\Config;

class Sanitiser
{
    private $purifier;

    /** @var Config */
    private $config;

    /**
     * @required
     */
    public function init(Config $config): void
    {
        $this->config = $config;
    }

    private function getPurifier(): \HTMLPurifier
    {
        if ($this->purifier) {
            return $this->purifier;
        }

        $purifierConfig = \HTMLPurifier_HTML5Config::create([
            'Cache.DefinitionImpl' => null,
            'HTML.SafeIframe' => true,
        ]);

        $allowedTags = implode(',', $this->config->get('general/htmlcleaner/allowed_tags')->all());
        $allowedAttributes = implode(',', $this->config->get('general/htmlcleaner/allowed_attributes')->all());
        $allowedFrameTargets = implode(',', $this->config->get('general/htmlcleaner/allowed_frame_targets')->all());

        $purifierConfig->set('HTML.AllowedElements', $allowedTags);
        $purifierConfig->set('HTML.AllowedAttributes', $allowedAttributes);
        $purifierConfig->set('Attr.AllowedFrameTargets', $allowedFrameTargets);

        $definition = $purifierConfig->maybeGetRawHTMLDefinition();
        $definition->addElement('super', 'Inline', 'Flow', 'Common', []);
        $definition->addElement('sub', 'Inline', 'Flow', 'Common', []);
        $definition->addAttribute('a', 'value', 'Text');
        $definition->addAttribute('a', 'frameborder', 'Text');
        $definition->addAttribute('a', 'allowfullscreen', 'Text');
        $definition->addAttribute('a', 'scrolling', 'Text');

        // Allow src tag in iframe for embed fields
        $definition->addAttribute('iframe', 'src', 'Text');

        $this->purifier = new \HTMLPurifier($purifierConfig);

        return $this->purifier;
    }

    public function clean(string $html): string
    {
        return $this->getPurifier()->purify($html);
    }
}
