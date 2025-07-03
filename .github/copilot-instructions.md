* Adhere to SOLID and DRY principles in all code to ensure maintainability and scalability.
* Write clean, readable, and well-structured code that is easy to understand and maintain, following "clean code" principles.
* Accompany all code with comprehensive unit tests, following established unit test guidelines such as:
    * Arrange-Act-Assert (AAA) pattern for test structure.
    * Clear and descriptive test names.
    * Testing both positive and negative scenarios.
    * Isolating unit tests from external dependencies using mocks, stubs or fakes.
    * Ensuring tests are repeatable and independent.
    * Maintaining high code coverage without sacrificing test quality.
    * Keeping tests fast and focused on a single responsibility.
* Use conventional commit messages, strictly following the conventional commit message guidelines.
    * Begin each commit message with a type, such as `feat`, `fix`, `docs`, `style`, `refactor`, `test`, or `chore`.
    * Optionally include a scope in parentheses to clarify the area affected, e.g., `feat(auth):`.
    * Write a short, imperative summary after the type/scope, separated by a colon and a space.
    * Use the body to provide additional context, motivation, or background for the change, if necessary.
    * Ensure each commit message is clear, concise, and accurately describes the change.
    * Avoid ambiguous or generic commit messages such as "update" or "fixes".
* Include clear and concise docblocks for all classes, methods, and functions, in line with docblock standards.
* Do not use global WordPress functions such as `get_option()` and `update_option()`. Instead, utilize the `\WpService\WpService` to promote testability and separation of concerns.
* Utilize contracts in the `\WpService\Contracts` namespace for interface segregation when in need to use WordPress functions.