<?php

namespace Mitsuki\Controller\Resolvers;

use Mitsuki\Attributes\Controller;
use Mitsuki\Attributes\Route;
use Mitsuki\Contracts\Controllers\ControllerResolverInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

/**
 * Class ControllerResolver
 *
 * Responsible for the automatic discovery of controller classes within the project.
 * It recursively scans the filesystem to identify PHP classes that qualify as
 * controllers based on the presence of specific PHP 8 Attributes.
 *
 * @author Zgenius Matondo <zgeniuscoders@gmail.com>
 */
class ControllerResolver implements ControllerResolverInterface
{
    /**
     * ControllerResolver Constructor.
     *
     * @param string $projectRoot The absolute path to the source directory to scan (e.g., /app/src).
     */
    public function __construct(
        private string $projectRoot
    )
    {
    }

    /**
     * Scans the root directory to extract Fully Qualified Class Names (FQCN) of controllers.
     *
     * A class is identified as a controller if:
     * 1. It is decorated with the #[Controller] class attribute.
     * 2. OR it contains at least one method decorated with the #[Route] attribute.
     *
     * @return array A unique list of discovered Fully Qualified Class Names (FQCN).
     * @throws \ReflectionException If an error occurs during class reflection.
     */
    public function resolve(): array
    {
        $controllers = [];

        if (!is_dir($this->projectRoot)) {
            return [];
        }

        // Recursive iteration over all files in the directory
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->projectRoot));

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */

            // Skip directories, non-PHP files, and the 'vendor' directory
            if ($file->isDir() || $file->getExtension() !== 'php' || str_contains($file->getPathname(), 'vendor')) {
                continue;
            }

            // Extract FQCN (Namespace + Class name) from the file content
            $fqn = $this->getFullyQualifiedClassName($file->getPathname());

            if ($fqn && class_exists($fqn)) {
                $reflection = new ReflectionClass($fqn);

                // Criterion 1: #[Controller] attribute on the class
                $hasController = !empty($reflection->getAttributes(Controller::class));

                // Criterion 2: At least one #[Route] attribute on class methods
                $hasRoute = false;
                foreach ($reflection->getMethods() as $method) {
                    if (!empty($method->getAttributes(Route::class))) {
                        $hasRoute = true;
                        break;
                    }
                }

                if ($hasController || $hasRoute) {
                    $controllers[] = $fqn;
                }
            }
        }

        return array_unique($controllers);
    }

    /**
     * Extracts the Namespace and Class name by parsing the PHP file.
     * * This method performs a raw file read and uses regular expressions to find
     * 'namespace' and 'class' declarations without including the file via the autoloader.
     *
     * @param string $filePath Absolute path to the PHP file.
     * @return string|null The FQCN or null if no class is found.
     */
    private function getFullyQualifiedClassName(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);

        $namespace = preg_match('/namespace\s+(.+?);/', $contents, $m) ? $m[1] : '';
        $class = preg_match('/class\s+(\w+)/', $contents, $m) ? $m[1] : '';

        return $class ? ($namespace ? $namespace . '\\' . $class : $class) : null;
    }
}