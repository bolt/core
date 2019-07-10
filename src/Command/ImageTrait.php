<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Output\OutputInterface;

trait ImageTrait
{
    public function outputImage(OutputInterface $output): void
    {
        if (getenv('TERM_PROGRAM') !== 'iTerm.app') {
            return;
        }

        $image = $this->getImage();

        $output->write($image);
    }

    public function getImage(): string
    {
        $filename = dirname(dirname(__DIR__)) . '/assets/static/logos/bolt_logo_cli.png';
        $imageFile = base64_encode(file_get_contents($filename));

        return $this->unicodeString('\u001B]1337;File=inline=1;width=auto;height=auto;preserveAspectRatio=1:' . $imageFile . '\u0007');
    }

    private function unicodeString(string $str, ?string $encoding = null): string
    {
        if ($encoding === null) {
            $encoding = ini_get('mbstring.internal_encoding');
        }

        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', function ($match) use ($encoding) {
            return mb_convert_encoding(pack('H*', $match[1]), $encoding, 'UTF-16BE');
        }, $str);
    }
}
