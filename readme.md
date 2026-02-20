# Mitsuki Controller

**Core controller layer for the Mitsuki Framework.**

This package provides the base controller abstraction and the automatic controller discovery system used by the Mitsuki Router. It integrates seamlessly with the `mitsuki/attributes` package to build your routing table from PHP 8 Attributes.

---

## ✨ Features

* **Automatic Controller Discovery**: Recursively scans your project to find valid controllers.
* **Attribute-Driven**: Fully compatible with `#[Controller]` and `#[Route]` attributes.
* **Zero Configuration**: No manual controller registration required.
* **Lightweight & Framework-Oriented**: Designed specifically for the Mitsuki ecosystem.
* **Tested & Reliable**: Includes automated tests for discovery edge cases.

---

## 🚀 Installation

Install via Composer:

```bash
composer require mitsuki/controller
```

> Requires `mitsuki/attributes` for controller and route metadata.

---

## 🛠 Usage

### 1️⃣ BaseController

The `BaseController` provides convenient helper methods to create HTTP responses.

```php
use Mitsuki\Controller\BaseController;

class UserController extends BaseController
{
    public function index()
    {
        return $this->response('<h1>Hello World</h1>');
    }

    public function api()
    {
        return $this->json(['status' => 'ok']);
    }
}
```

### Available Helpers

#### `response($body, int $status = 200, array $headers = []): Response`

Creates a standard HTTP response.

* Default `Content-Type`: `text/html`
* Fully customizable headers

#### `json($data, int $status = 200, array $headers = []): JsonResponse`

Creates a JSON response.

* Automatically encodes data to JSON
* Automatically sets `Content-Type: application/json`

---

### 2️⃣ ControllerResolver

The `ControllerResolver` is responsible for automatically discovering controllers inside your project.

```php
use Mitsuki\Controller\Resolvers\ControllerResolver;

$resolver = new ControllerResolver(__DIR__ . '/src');
$controllers = $resolver->resolve();
```

---

## 🔍 How Controller Discovery Works

A class is considered a controller if:

1. It is decorated with `#[Controller]`
2. **OR** it contains at least one method decorated with `#[Route]`

The resolver:

1. Recursively scans the provided root directory.
2. Ignores:

    * Non-PHP files
    * Directories
    * The `vendor/` directory
3. Extracts the Fully Qualified Class Name (FQCN).
4. Uses PHP `ReflectionClass` to inspect attributes.
5. Returns a unique list of valid controllers.

---

## 🏗 Architecture

```
Project Source Directory
        ↓
ControllerResolver
        ↓
ReflectionClass
        ↓
#[Controller] / #[Route]
        ↓
Mitsuki Router builds routing table
```

This keeps your routing system:

* Decoupled
* Clean
* Fully metadata-driven

---

## 🧪 Testing

The package includes automated tests that verify:

* ✅ Detection of classes with `#[Controller]`
* ✅ Detection of classes with `#[Route]` methods
* ✅ Ignoring classes without Mitsuki attributes
* ✅ Ignoring the `vendor/` directory
* ✅ Handling invalid directories safely

Example test scenario:

```php
#[Controller('/admin')]
class AdminController {}
```

```php
class BlogController {
    #[Route('blog_index', '/blog', ['GET'])]
    public function index() {}
}
```

---

## 📦 Package Structure

```
src/
 ├── BaseController.php
 └── Resolvers/
     └── ControllerResolver.php
```

---

## 🤝 Contributing

To contribute:

1. Fork the repository.
2. Improve or extend the resolver logic.
3. Add or update tests.
4. Submit a Pull Request.

---

## 📄 License

This project is licensed under the MIT License.

---

**Maintained by Zgenius Matondo**
GitHub: [https://github.com/zgeniuscoders](https://github.com/zgeniuscoders)
