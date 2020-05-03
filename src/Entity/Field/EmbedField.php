<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class EmbedField extends Field implements FieldInterface
{
    public const TYPE = 'embed';

    public function getValue(): ?array
    {
        $value = parent::getValue();
        $value['responsive'] = $this->getResponsive();
        $value['responsive_inline'] = $this->getResponsiveInline();

        return $value;
    }

    private function getResponsive(): string
    {
        $html = parent::getValue()['html'] ?? '';

        if (empty($html)) {
            return '';
        }

        $html = preg_replace("/width=(['\"])([0-9]+)(['\"])/i", '', $html);
        $html = preg_replace("/height=(['\"])([0-9]+)(['\"])/i", '', $html);

        return '<div class="embed-responsive">' . $html . '</div>';
    }

    private function getResponsiveInline(): string
    {
        $html = parent::getValue()['html'] ?? '';

        if (empty($html)) {
            return '';
        }

        $wrapperOpening = '<div style="overflow: hidden; padding-bottom: 56.25%; position: relative; height: 0;">';
        $wrapperClosing = '</div>';

        $inline = 'style="left: 0; top: 0; height: 100%; width: 100%; position: absolute;"';

        $html = preg_replace("/width=(['\"])([0-9]+)(['\"])/i", $inline, $html);
        $html = preg_replace("/height=(['\"])([0-9]+)(['\"])/i", '', $html);

        return $wrapperOpening . $html . $wrapperClosing;
    }
}
