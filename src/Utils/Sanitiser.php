<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Configuration\Config;

class Sanitiser
{
    private $purifier;

    public function __construct(?Config $config = null)
    {
        $purifierConfig = \HTMLPurifier_HTML5Config::create([
            'Cache.DefinitionImpl' => null,
            'HTML.SafeIframe' => true,
        ]);

        if ($config) {
            $allowedTags = implode(',', $config->get('general/htmlcleaner/allowed_tags')->all());
            $allowedAttributes = implode(',', $config->get('general/htmlcleaner/allowed_attributes')->all());
            $purifierConfig->set('HTML.AllowedElements', $allowedTags);
            $purifierConfig->set('HTML.AllowedAttributes', $allowedAttributes);
        }

        $definition = $purifierConfig->maybeGetRawHTMLDefinition();
        $definition->addElement('super', 'Inline', 'Flow', 'Common', []);
        $definition->addElement('sub', 'Inline', 'Flow', 'Common', []);
        $definition->addAttribute('a', 'value', 'Text');
        $definition->addAttribute('a', 'frameborder', 'Text');
        $definition->addAttribute('a', 'allowfullscreen', 'Text');
        $definition->addAttribute('a', 'scrolling', 'Text');

        $this->purifier = new \HTMLPurifier($purifierConfig);
    }

    public function clean(string $html): string
    {
        return $this->purifier->purify($html);
    }
}
