# Education LMS Development Guidelines

## Core Principles
**Follow Laravel conventions first.** If Laravel has a documented way to do something, use it. Only deviate when you have a clear justification.

## Build/Configuration Instructions

### Prerequisites
- PHP 8.4 or higher
- Composer
- Node.js and npm
- SQLite (for development/testing)

### Initial Setup
1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install JavaScript dependencies:
   ```bash
   npm install
   ```
4. Create environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Set up the database:
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

### Development Environment
Run the development server with:
```bash
composer dev
```

This command starts:
- Laravel development server
- Queue worker
- Log watcher
- Vite development server

For server-side rendering, use:
```bash
composer dev:ssr
```

## Testing Framework & Standards

### Testing Framework
The project uses Pest PHP for testing, which provides a more expressive syntax built on PHPUnit. Never use PHPUnit

### Running Tests
```bash
# Run all tests
composer test

# Run specific test suites
composer test:unit          # Unit tests with coverage
composer test:type-coverage # Type coverage tests
composer test:types         # Static analysis
composer test:lint          # Code style checks
composer test:refactor      # Refactoring checks

# Run specific test file
./vendor/bin/pest tests/path/to/TestFile.php --coverage
```

### Creating Tests
1. **Test Organization**:
    - `tests/Feature/Console` - Console command tests
    - `tests/Feature/Http` - HTTP-related tests (Controllers, requests, etc.)
    - `tests/Unit/Actions` - Action classes
    - `tests/Unit/Models` - Model tests
    - `tests/Unit/Jobs` - Job tests
    - `tests/Unit/Policies` - Policy tests
    - `tests/Unit/Services` - Service classes

2. **Test Naming**: Files should follow `*Test.php` convention

3. **Test Structure** - Follow Arrange-Act-Assert pattern:
```php
<?php

declare(strict_types=1);

use App\Models\YourModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can perform some action', function (): void {
    // Arrange
    $data = [
        'field' => 'value',
    ];

    // Act
    $result = YourModel::factory()->create($data);

    // Assert
    expect($result)->toBeInstanceOf(YourModel::class)
        ->and($result->field)->toBe('value');
});
```

4. **Database Testing**: Use `RefreshDatabase` trait for database interactions
5. **Factories**: Generate a `{Model}Factory` with each model

## PHP Standards & Code Style

### Core PHP Standards
- Follow PSR-1, PSR-2, and PSR-12
- Use strict types declaration: `declare(strict_types=1);`
- Classes should be final when possible
- Use PHP v8.4 features
- Enforce strict types and array shapes via PHPStan
- Use camelCase for non-public-facing strings
- Use short nullable notation: `?string` not `string|null`
- Always specify `void` return types when methods return nothing

### Class Structure
```php
final class SomeClass
{
    public function __construct(
        private string $name,
        private int $age,
    ) {
    }
}
```

### Type Declarations & Docblocks
- Use typed properties over docblocks
- Specify return types including `void`
- Use short nullable syntax: `?Type` not `Type|null`
- Document iterables with generics:
  ```php
  /** @return Collection<int, User> */
  public function getUsers(): Collection
  ```

#### Docblock Rules
- Don't use docblocks for fully type-hinted methods (unless description needed)
- **Always import classnames in docblocks** - never use fully qualified names:
  ```php
  use Spatie\Url\Url;
  /** @return Url */
  ```
- Use one-line docblocks when possible: `/** @var string */`
- Most common type should be first in multi-type docblocks
- For iterables, always specify key and value types:
  ```php
  /**
   * @param array<int, MyObject> $myArray
   * @param int $typedArgument 
   */
  function someFunction(array $myArray, int $typedArgument) {}
  ```
- Use array shape notation for fixed keys:
  ```php
  /** @return array{
     first: SomeClass, 
     second: SomeClass
  } */
  ```

### Control Flow - Happy Path Pattern
- **Happy path last**: Handle error conditions first, success case last
- **Avoid else**: Use early returns instead of nested conditions
- **Separate conditions**: Prefer multiple if statements over compound conditions
- **Always use curly brackets** even for single statements

```php
// Happy path last
if (! $user) {
    return null;
}

if (! $user->isActive()) {
    return null;
}

// Process active user...

// Short ternary
$name = $isFoo ? 'foo' : 'bar';

// Multi-line ternary
$result = $object instanceof Model ?
    $object->name :
    'A default value';
```

### Strings & Formatting
Use string interpolation over concatenation:
```php
// Good
$greeting = "Hello {$name}";

// Avoid
$greeting = 'Hello ' . $name;
```

### Code Quality Tools
```bash
# Fix code style issues
composer lint

# Run static analysis
composer test:types

# Apply refactoring
composer refactor
```

## Laravel Conventions

### Routes
- URLs: kebab-case (`/open-source`)
- Route names: camelCase (`->name('openSource')`)
- Parameters: camelCase (`{userId}`)
- Use tuple notation: `[Controller::class, 'method']`

### Controllers
- Singular resource names (`PostController`)
- Controllers should not have public methods besides '__construct', '__invoke', 'index', 'show', 'create', 'store', 'edit', 'update', 'destroy', 'middleware'
- **Single Action Controllers**: Use `__invoke` method and register directly:
  ```php
  Route::get('dashboard', DashboardController::class)->name('dashboard');
  ```
- **Redirects**: Always use `to_route()` for redirects to named routes:
  ```php
  return to_route('dashboard')->with('success', 'Action completed');
  ```

### Configuration
- Files: kebab-case (`pdf-generator.php`)
- Keys: snake_case (`chrome_path`)
- Add service configs to `config/services.php`, don't create new files
- Use `config()` helper, avoid `env()` outside config files

### Artisan Commands
- Names: kebab-case (`delete-old-records`)
- Always provide feedback (`$this->comment('All ok!')`)
- Show progress for loops, summary at end
- Put output BEFORE processing item:
  ```php
  $items->each(function(Item $item) {
      $this->info("Processing item id `{$item->id}`...");
      $this->processItem($item);
  });
  
  $this->comment("Processed {$items->count()} items.");
  ```

### Validation & Requests
- Use FormRequest for validation
- Name with Create, Update, Delete
- Use array notation for multiple rules:
  ```php
  public function rules() {
      return [
          'email' => ['required', 'email'],
      ];
  }
  ```

### Actions Pattern
- Use Action classes for business logic
- Wrap database operations in transactions:
  ```php
  public function handle(): void
  {
      DB::transaction(function (): void {
          // Database operations
      });
  }
  ```
- Action naming and usage:
  ```php
  public function store(CreateTodoRequest $request, CreateTodoAction $action)
  {
      $user = $request->user();
      $action->handle($user, $request->validated());
  }
  ```

## React/Frontend Conventions

### File Structure & Naming
- All file paths: lowercase (`resources/js/pages/courses/index.tsx`)
- Components: `{Model}{Action}Page` pattern (`CourseIndexPage`, `CourseShowPage`)
- Props interfaces: `{PageName}Props` (`CourseIndexPageProps`)

### Routes in React Components
Never use hardcoded routes:
```tsx
// Incorrect
<Link href="/dashboard">Dashboard</Link>

// Correct
<Link href={route('dashboard')}>Dashboard</Link>
```

### Styling & UI
- Use Shadcn for UI components
- Use Tailwind CSS for styling
- Keep UI minimal

### JavaScript/TypeScript Standards
- Single quotes
- Semicolons required
- 4 spaces for indentation (except YAML: 2 spaces)
- 150 character line length

## Database & Architecture

### Database
- Migrations for all database changes
- Use Laravel's Eloquent ORM
- Use enums for fields with fixed values
- Avoid `DB::`; use `Model::query()` only
- **Migrations**: Do not write down methods, only up methods

### Architecture Guidelines
- Follow Laravel's MVC architecture
- Use Actions classes for business logic
- Use Policies for authorization
- Use Enums for type-safe constants
- Delete .gitkeep when adding files
- Stick to existing structure—no new folders
- No dependency changes without approval

### Authorization
- Policies use camelCase: `Gate::authorize('editPost', ...)`

## Enums
Use PascalCase for enum values:
```php
enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
```

## Comments & Documentation
- **Avoid comments** - write expressive code instead
- When needed, use proper formatting:
  ```php
  // Single line with space after //
  
  /*
   * Multi-line blocks start with single *
   */
  ```
- Refactor comments into descriptive function names

## Whitespace & Formatting
- Add blank lines between statements for readability
- Exception: sequences of equivalent single-line operations
- No extra empty lines between `{}` brackets
- Let code "breathe" - avoid cramped formatting

## Blade Templates
- Indent with 4 spaces
- No spaces after control structures:
  ```blade
  @if($condition)
      Something
  @endif
  ```

## API Design
- Use plural resource names: `/errors`
- Use kebab-case: `/error-occurrences`
- Limit deep nesting for simplicity

## Quick Reference - Naming Conventions

### General Naming
- **Classes**: PascalCase (`UserController`, `OrderStatus`)
- **Methods/Variables**: camelCase (`getUserName`, `$firstName`)
- **Routes**: kebab-case (`/open-source`, `/user-profile`)
- **Config files**: kebab-case (`pdf-generator.php`)
- **Config keys**: snake_case (`chrome_path`)
- **Artisan commands**: kebab-case (`php artisan delete-old-records`)

### Laravel Specific
- **Controllers**: Singular resource name + `Controller` (`PostController`)
- **Views**: camelCase (`openSource.blade.php`)
- **Jobs**: action-based (`CreateUser`, `SendEmailNotification`)
- **Events**: tense-based (`UserRegistering`, `UserRegistered`)
- **Listeners**: action + `Listener` suffix (`SendInvitationMailListener`)
- **Commands**: action + `Command` suffix (`PublishScheduledPostsCommand`)
- **Mailables**: purpose + `Mail` suffix (`AccountActivatedMail`)
- **Resources/Transformers**: plural + `Resource`/`Transformer` (`UsersResource`)
- **Enums**: descriptive name, no prefix (`OrderStatus`, `BookingType`)

## Development Workflow Commands

```bash
# Start development environment
composer dev

# Testing
composer test
composer test:unit
composer test:types

# Code quality
composer lint
composer refactor

# Type checking
composer test:type-coverage
```
