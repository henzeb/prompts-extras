# CLAUDE.md - Development Guide

## Commands
- **Run all tests**: `vendor/bin/pest`
- **Run a single test**: `vendor/bin/pest tests/path/to/TestFile.php`
- **Run test with coverage**: `XDEBUG_MODE=coverage vendor/bin/pest --coverage-html coverage`
- **Run mutation tests**: `XDEBUG_MODE=coverage vendor/bin/pest --mutate --covered-only`

## Code Style Guidelines
- **PHP Version**: 8.1+
- **Formatting**: PSR-12 standard, 4 space indentation
- **Namespace**: `Henzeb\Prompts`
- **Class Naming**: PascalCase, descriptive names
- **Method Naming**: camelCase, descriptive of functionality
- **Types**: Always use proper type hints and return types
- **Error Handling**: Use exceptions for exceptional cases
- **Comments**: Document complex logic
- **Testing**: Use Pest's functional style testing, with clear test descriptions
- **Dependencies**: Follows Laravel's patterns and conventions
- **Imports**: Group imports by type (PHP core, package, project)

This package extends Laravel Prompts with additional functionality. Follow established patterns when contributing new features.