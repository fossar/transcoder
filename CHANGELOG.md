# Transcoder Changelog

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] – unreleased

- Mb: Make encoding detection stricter.
- Raise minimum PHP version to 7.4.0.


## [2.0.0] – 2023-03-07

- Iconv: Fix warning on PHP 8.2 when passing `null` as source encoding.
- Raise minimum PHP version to 7.2.5.
- Add parameter and return type hints.


## [1.0.1] – 2021-05-23

The project has been revived and is now available under the name [`fossar/transcoder`](https://packagist.org/packages/fossar/transcoder). This is a first release since the fork.

- Iconv: Fixed detection of unsupported encoding on PHP 8.
- Mb: Restored the ability to pass “auto” as input language on PHP ≥ 7.3.
- Fixed the pairing of setting and restoring error handlers in the transcoders so that they do not clear handlers that they did not create nor do they leave any around when something unexpected happens.
- Fixed creating `Transcoder` with just `mbstring` (without `iconv`).
- Clarified that “auto-detection” does not actually work.
- Fixed various minor test issues.
- Cleaned up coding style.
- Added Nix expression for easier development and sharing the environment with CI.
- Switched to GitHub Actions for CI and added more PHP versions.

[3.0.0]: https://github.com/fossar/transcoder/compare/v2.0.0...v3.0.0
[2.0.0]: https://github.com/fossar/transcoder/compare/v1.0.1...v2.0.0
[1.0.1]: https://github.com/fossar/transcoder/compare/1.0.0...v1.0.1
