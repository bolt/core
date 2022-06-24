<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use HTMLPurifier;
use HTMLPurifier_HTML5Config;

class Sanitiser
{
    /** @var HTMLPurifier|null */
    private $purifier = null;

    /** @var Config */
    private $config;

    /**
     * @required
     */
    public function init(Config $config): void
    {
        $this->config = $config;
    }

    private function getPurifier(): HTMLPurifier
    {
        if ($this->purifier) {
            return $this->purifier;
        }

        $purifierConfig = HTMLPurifier_HTML5Config::create([
            'Cache.DefinitionImpl' => null,
            'HTML.SafeIframe' => true,
        ]);

        $allowedTags = implode(',', $this->config->get('general/htmlcleaner/allowed_tags')->all());
        $allowedAttributes = implode(',', $this->config->get('general/htmlcleaner/allowed_attributes')->all());
        $allowedFrameTargets = implode(',', $this->config->get('general/htmlcleaner/allowed_frame_targets')->all());

        $purifierConfig->set('HTML.AllowedElements', $allowedTags);
        $purifierConfig->set('HTML.AllowedAttributes', $allowedAttributes);
        $purifierConfig->set('Attr.AllowedFrameTargets', $allowedFrameTargets);

        if (in_array('id', $this->config->get('general/htmlcleaner/allowed_attributes')->all(), true)) {
            $purifierConfig->set('Attr.EnableID', true);
        }

        $definition = $purifierConfig->maybeGetRawHTMLDefinition();
        $definition->addElement('super', 'Inline', 'Flow', 'Common', []);
        $definition->addElement('sub', 'Inline', 'Flow', 'Common', []);
        $definition->addAttribute('a', 'value', 'Text');
        $definition->addAttribute('a', 'frameborder', 'Text');
        $definition->addAttribute('a', 'allowfullscreen', 'Text');
        $definition->addAttribute('a', 'scrolling', 'Text');
        $definition->addAttribute('td', 'width', 'Text');
        $definition->addAttribute('img', 'style', 'Text');

        // Allow src tag in iframe for embed fields
        $definition->addAttribute('iframe', 'src', 'Text');

        // Create non supported elements
        $this->createNonSupportedElements($definition, explode(',',$allowedTags));


        $this->purifier = new \HTMLPurifier($purifierConfig);

        return $this->purifier;
    }

    public function clean(string $html): string
    {
        return $this->getPurifier()->purify($html);
    }

    /**
     * Handles the creation of non-supported HTML elements by HTMLPurifier out of the box
     */
    private function createNonSupportedElements(?\HTMLPurifier_Definition $definition, array $allowedTags)
    {
        if(array_search('svg', $allowedTags)){
            $definition->addElement('svg', 'Block', 'Flow', 'Common');
        }
    }
}
