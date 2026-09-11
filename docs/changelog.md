# Changelog

## 0.1.4

* Added `let()`/`set()` declarative helpers and parent-chain value lookup in `TestCase`
* Refactored `GlobalFunctions.php`: global wrappers throw when a target name is already
  taken while globals are enabled
* Renamed the examples namespace to `ProbatioExamples`; added an animals hierarchy and
  `NestedContextTest` covering nested-group state scoping
* Added a `tests/` stub for the library itself (`tests/Unit/SomeTest.php`)
* Bumped the dev PHP version in the Justfile to 8.5

## 0.1.3

* Fixed `not` property behavior of the expectations
* Added new expectations (`toBe`, `toBeBetween`, `toBeEmpty`)
* Added new examples
* Added architecture, usage, and future-improvements documentation

## 0.1.2

* Added expectations: `toBeBetween`, `toBeEmpty`, and `toBe` fix
* Added new examples
* Added GitHub Actions CI/CD pipeline
* Added pre-commit hooks
* Added tests badge
* Updated package tags and readme

## 0.1.1

* Massive internal library code refactoring
* Published to the Packagist.org

## 0.1.0

* The project's basics
