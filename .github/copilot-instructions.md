General Principles:
- Adhere to SOLID principles for maintainable, scalable, and robust software design.
- Follow the DRY (Don't Repeat Yourself) principle to avoid code duplication and improve maintainability.
- Write clean, readable, and well-structured code, in line with Clean Code principles.
- Prioritize readability and clear naming over brevity; avoid abbreviations.
- Use camelCase for variable, method, and function names where applicable.

Testing Standards:
- All code must be accompanied by comprehensive unit tests.
- Use Arrange-Act-Assert (AAA) pattern for test structure.
- Use clear and descriptive test method names.
- Cover both positive and negative scenarios.
- Isolate tests from external dependencies using mocks, stubs, or fakes.
- Tests must be repeatable, independent, fast, and focused on a single responsibility.
- Strive for high code coverage without sacrificing test quality.

Documentation:
- Include docblocks for all classes, methods, and functions.
- Follow established PHPDoc standards.
- Clearly explain: purpose, parameters, return types, and exceptions thrown (if any).

Commit Message Guidelines:
- Follow Conventional Commits (https://www.conventionalcommits.org/en/v1.0.0/) format.
- Example: feat(acf-service): add support for flexible content blocks
- Example: fix(image-component): resolve blur-up image loading issue

WordPress Integration:
- Do NOT use global WordPress functions such as get_option() or update_option() directly.
- Use the \WpService\WpService class to encapsulate WordPress logic.
- Use interfaces from \WpService\Contracts for interface segregation, dependency inversion, and improved testability.

ACF Integration:
- Do NOT use global ACF functions such as get_field() or update_field() directly.
- Use the \AcfService\AcfService to encapsulate Advanced Custom Fields (ACF) logic.
- Use interfaces from \AcfService\Contracts for interface segregation, dependency inversion, and improved testability.

Code Style & Architecture:
- Backend (PHP):
  - Follow PSR-12 coding standards (https://www.php-fig.org/psr/psr-12/).
  - Use modular, loosely-coupled components.
  - Prefer dependency injection over manual instantiation.
- Frontend (CSS/HTML/JS):
  - CSS Naming: BEM (Block-Element-Modifier)
  - CSS Architecture: ITCSS (Inverted Triangle CSS)
  - UI Structure: Atomic Design

Versioning & Change Management:
- Use Semantic Versioning (https://semver.org/).
- Maintain changelogs using Keep a Changelog (https://keepachangelog.com/en/1.0.0/).

Public Sector Standards:
- Align code and documentation with the Foundation for Public Code (https://standard.publiccode.net/) for long-term maintainability and openness in public digital infrastructure.