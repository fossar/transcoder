# Transcoder Changelog

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/fossar/transcoder/compare/1.0.0...HEAD
