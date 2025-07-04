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
* Include clear and concise docblocks for all classes, methods, and functions, in line with docblock standards.
* Do not use global WordPress functions such as `get_option()` and `update_option()`. Instead, utilize the `\WpService\WpService` to promote testability and separation of concerns.
* Utilize contracts in the `\WpService\Contracts` namespace for interface segregation when in need to use WordPress functions.