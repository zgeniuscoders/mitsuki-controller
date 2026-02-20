<?php

use Mitsuki\Controller\Resolvers\ControllerResolver;

/**
 * Helpers pour gérer les fichiers temporaires
 */
function createTestFile(string $path, string $content): void {
    $fullPath = sys_get_temp_dir() . '/mitsuki_tests/' . $path;
    $dir = dirname($fullPath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    file_put_contents($fullPath, "<?php\n" . $content);
    // On charge le fichier pour que ReflectionClass le trouve
    require_once $fullPath;
}

beforeEach(function () {
    $this->testDir = sys_get_temp_dir() . '/mitsuki_tests';
    if (is_dir($this->testDir)) {
        exec("rm -rf " . escapeshellarg($this->testDir));
    }
    mkdir($this->testDir, 0777, true);
});

afterEach(function () {
    exec("rm -rf " . escapeshellarg($this->testDir));
});

/**
 * Tests
 */

test('it resolves classes decorated with Controller attribute', function () {
    createTestFile('AdminController.php', '
        namespace App\Controllers;
        use Mitsuki\Attributes\Controller;
        
        #[Controller("/admin")]
        class AdminController {}
    ');

    $resolver = new ControllerResolver($this->testDir);
    $results = $resolver->resolve();

    expect($results)->toContain('App\Controllers\AdminController')
        ->and($results)->toHaveCount(1);
});

test('it resolves classes containing Route attributes on methods', function () {
    createTestFile('BlogController.php', '
        namespace App\Controllers;
        use Mitsuki\Attributes\Route;
        
        class BlogController {
            #[Route("blog_index", "/blog", ["GET"])]
            public function index() {}
        }
    ');

    $resolver = new ControllerResolver($this->testDir);
    $results = $resolver->resolve();

    expect($results)->toContain('App\Controllers\BlogController');
});

test('it ignores classes without Mitsuki attributes', function () {
    createTestFile('PlainService.php', '
        namespace App\Services;
        class PlainService {}
    ');

    $resolver = new ControllerResolver($this->testDir);
    expect($resolver->resolve())->toBeEmpty();
});

test('it strictly ignores the vendor directory', function () {
    createTestFile('vendor/ExternalController.php', '
        namespace Vendor\Lib;
        use Mitsuki\Attributes\Controller;
        
        #[Controller("/ext")]
        class ExternalController {}
    ');

    $resolver = new ControllerResolver($this->testDir);
    expect($resolver->resolve())->toBeEmpty();
});

test('it returns an empty array for invalid directories', function () {
    $resolver = new ControllerResolver('/invalid/path/to/nowhere');
    expect($resolver->resolve())->toBeArray()->toBeEmpty();
});