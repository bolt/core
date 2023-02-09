<?php

use Symfony\Component\VarExporter\VarExporter;
use Webimpress\SafeWriter\FileWriter;

require dirname(dirname(dirname(dirname(__DIR__)))) . '/vendor/autoload.php';

class BundleFixer
{
    /** @var array */
    private $bundles;

    public function fix(): void
    {
        if (file_exists('../config/bundles.php')) {
            $path = '../config/bundles.php';
        } elseif (file_exists('config/bundles.php')) {
            $path = 'config/bundles.php';
        } else {
            echo 'Please run this command from your project root folder.';
            die();
        }

        if (! is_writable($path)) {
            echo "Please make sure 'config/bundles.php' is writable.";
            die();
        }

        $this->bundles = require_once $path;

        $changes = $this->addBundle('SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle', ['all' => true]);
        $changes = $this->addBundle('Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle', ['all' => true]) || $changes;
        $changes = $this->removeBundle('Symfony\Bundle\MakerBundle\MakerBundle') || $changes;
        $changes = $this->removeBundle('Symplify\ConsoleColorDiff\ConsoleColorDiffBundle') || $changes;

        if ($changes) {
            echo "Writing updated 'bundles.php'.\n";

            $output = "<?php\n\nreturn " . VarExporter::export($this->bundles) . ";\n";

            $writer = new FileWriter();
            $writer->writeFile($path, $output);
        } else {
            echo "No changes to 'bundles.php' required.\n";
        }
    }

    public function addBundle(string $key, array $value): bool
    {
        if (! array_key_exists($key, $this->bundles) && class_exists($key)) {
            $this->bundles[$key] = $value;
            echo " - Adding '{$key}'.\n";

            return true;
        }

        return false;
    }

    public function removeBundle(string $key): bool
    {
        if (array_key_exists($key, $this->bundles) && ! class_exists($key)) {
            unset($this->bundles[$key]);
            echo " - Removing '{$key}'.\n";

            return true;
        }

        return false;
    }
}

$fixer = new BundleFixer();

$fixer->fix();
