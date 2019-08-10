<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ORM\Entity
 */
class ImageField extends Field implements FieldInterface
{
    /** @var array */
    private $fieldBase = [];

    public function __construct()
    {
        $this->fieldBase = [
            'filename' => '',
            'alt' => '',
            'title' => '',
            'path' => '',
            'media' => '',
        ];
    }

    public function getType(): string
    {
        return 'image';
    }

    public function __toString(): string
    {
        return $this->getPath();
    }

    public function getValue(): array
    {
        $value = array_merge($this->fieldBase, (array) parent::getValue() ?: []);

        // Remove cruft field getting stored as JSON.
        unset($value[0]);

        // Generate a URL
        $value['path'] = $this->getPath();

        // @todo This needs to be injected, not created on the fly.
        $package = new PathPackage('/files/', new EmptyVersionStrategy());
        $request = Request::createFromGlobals();
        $value['url'] = $request->getUriForPath($package->getUrl($this->getPath()));

        return $value;
    }

    public function getPath(): string
    {
        $filesPackage = new PathPackage('/files/', new EmptyVersionStrategy());

        return $filesPackage->getUrl($this->get('filename'));
    }
}
