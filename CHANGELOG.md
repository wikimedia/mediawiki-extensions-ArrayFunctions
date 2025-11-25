CHANGELOG
=========

All notable changes to the ArrayFunctions project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [unreleased]

### Changed

- BREAKING: Remove support for MediaWiki 1.39.3 and earlier. The oldest supported version is now MediaWiki 1.39.4.

## [v1.17.0] - 2025-08-26

> Note: This version is not tagged properly on `Special:Version`, where it still shows as `1.16.2`.

### Added

- Add the `$wgArrayFunctionsMaxPipelineLength` configuration variable to limit the total number of steps in a pipeline.
- Add the `Pages using the ArrayFunctions extension` (`af-tracking-category`) tracking category.

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

### Fixed

- Ranges that exceed `$wgArrayFunctionsMaxRangeSize` are now taken into account when reporting the size of the largest
  constructed range in the parser limit report. Previously, only ranges that did not exceed the limit would be taken
  into account.
- Fix `TypeError` that occurred when an error was propagated from a nested parser function call.

## [v1.16.2] - 2025-06-28

### Fixed

- Fix exception caused by storing a `Message` object in the parser's extension data ([T397946](https://phabricator.wikimedia.org/T397946)).
- Ensure errors are rendered in the page's target language instead of the user's language ([T397951](https://phabricator.wikimedia.org/T397951)).

## [v1.16.1] - 2025-06-19

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

### Fixed

- Fix `BadMethodCallException` that was caused by relying on `$wgLang` in the `ParserLimitReportPrepare` hook.

## [v1.16.0] - 2025-06-10

### Added

- Add the `$wgArrayFunctionsForeachIterationLimit` configuration variable to limit the total number of iterations that
  can be performed by `#af_foreach`.
- Add the `$wgArrayFunctionsMaxRangeSize` configuration variable to limit the maximum number of elements that an array
  constructed using `#af_range` can contain.
- Add the `#af_zip` parser function.
- Add the `!` overloaded index to show a value.
- Add the `table` and `simple` formats for `#af_show`.
- Add the `Pages with ArrayFunctions errors` (`af-error-category`) tracking category.
- Add error tracking and propagation.

### Changed

- The `#af_sort` and `#af_ksort` parser functions now compare items normally instead of as strings.
- Allow `#af_show` to take a format.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

### Fixed

- Fix exception when passing the empty string to a keyword argument.

## [v1.15.0] - 2025-05-13

### Added

- Add the `arrayfunctions` Cargo display format.

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.14.2] - 2025-04-15

### Fixed

- Remove properties without a value from the result when using the `arrayfunctions` result format with Semantic
  MediaWiki.

## [v1.14.1] - 2025-04-15

### Added

- Add compatibility with Semantic MediaWiki 5.0.0.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

### Fixed

- Restore compatibility with deprecated MediaWiki 1.35.

## [v1.14.0] - 2025-04-01

### Added

- Add the `arrayfunctions` Semantic MediaWiki result format.
- Add the `#af_pipeline` parser function.

### Changed

- Drop compatibility with MediaWiki 1.35.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

### Fixed

- Fix exception when using `#af_template` on MediaWiki 1.44 or later.

## [v1.13.0] - 2025-03-04

### Changed

- Replace the `#af_set` parser function with `#af_put` that has a more sane parameter order. The `#af_set` parser
  function remains available for backwards compatibility.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

### Fixed

- The magic variable IDs array is now no longer treated as associative, which would previously break CodeMirror syntax
  highlighting. (by alex4401)

## [v1.12.0] - 2025-02-04

### Added

- Add the `#af_range` parser function.

### Changed

- Rename the `#af_wildcard` parser function to `#af_group`, and add an alias for `#af_wildcard`.
- The `#af_difference` parser function now compares items normally instead of as strings.
- The `#af_exists` parser function now accepts multiple keys to check if a nested key exists.
- The `#af_instersect` parser function now compares items normally instead of as strings.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.11.0] - 2024-12-10

### Added

- Add the `#af_flatten` parser function.
- Add the `#af_wildcard` parser function.
- Add the `#af_reverse` parser function.
- Overload `#af_get` with special indices to perform certain operations on the array instead of retrieving a key.

### Changed

- The `#af_unique` parser function now compares items normally instead of as strings.

## [v1.10.0] - 2024-11-26

### Added

- Add ZLIB compression using `gzdeflate` and `gzinflate`.

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.9.0] - 2024-05-02

### Added

- Add `delimiter` option to the `#af_foreach` parser function.

### Changed

- All string parameters now support escape sequences.

## [v1.8.0] - 2024-04-04

### Changed

- Whitespace is now trimmed from the beginning and the end of array values to be consistent with the behaviour of other
  parameters in MediaWiki.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.7.0] - 2023-10-24

### Added

- Add the `#af_ksort` parser function.
- Add `caseinsensitive` option to the `#af_keysort` parser function.
- Add `caseinsensitive` option to the `#af_sort` parser function.

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.6.0] - 2023-09-12

### Added

- Add the `#af_stringmap` parser function.
- Add the `mw.af.import` Lua function to import ArrayFunctions arrays into Lua.

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.5.0] - 2023-09-07

### Added

- Add the `#af_difference` parser function.

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.4.4] - 2023-06-30

### Changed

- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

### Fixed

- Fix `#af_template` parser function to no longer check a user their read permissions explicitly, as regular template
  transclusion also does not do this. Previously, an error would be outputted.

## [v1.4.3] - 2023-05-26

### Changed

- Exporting a `NULL` value (e.g. through the `mw.af.export` Lua function) will now result in the empty string.
  Previously, the `NULL` would be returned unaltered.

## [v1.4.2] - 2023-05-26

### Changed

- The `#af_template` parser function now shows non-existent templates as a broken link. Previously, an error would be
  outputted.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.4.1] - 2023-05-05

### Changed

- The `#af_split` parser function now allows the empty string as its first parameter. Previously, an error would be
  outputted.
- Localisation updates courtesy of [translatewiki.net](https://translatewiki.net).

## [v1.4.0] - 2023-04-26

### Added

- Add the `#af_show` parser function.

## [v1.3.0] - 2023-03-27

### Added

- Add the `#af_search` parser function.

## [v1.2.0] - 2023-03-03

### Added

- Add the `#af_intersect` parser function.
- Add the `#af_merge` parser function.
- Add the `#af_reduce` parser function.

## [v1.1.0] - 2023-02-03

### Added

- Add the `#af_split` parser function.

## [v1.0.1] - 2023-01-09

### Changed

- The `mw.af.export` Lua function now supports parameters of all types. Previously, it only supported arrays.

### Fixed

- Fix issue where an exception was thrown when a parameter with an incorrect type was passed to `mw.af.export`.

## [v1.0.0] - 2023-01-07

### Added

- Add the `#af_bool` parser function.
- Add the `#af_count` parser function.
- Add the `#af_exists` parser function.
- Add the `#af_float` parser function.
- Add the `#af_foreach` parser function.
- Add the `#af_get` parser function.
- Add the `#af_if` parser function.
- Add the `#af_int` parser function.
- Add the `#af_isarray` parser function.
- Add the `#af_join` parser function.
- Add the `#af_keysort` parser function.
- Add the `#af_list` parser function.
- Add the `#af_map` parser function.
- Add the `#af_object` parser function.
- Add the `#af_print` parser function.
- Add the `#af_push` parser function.
- Add the `#af_set` parser function.
- Add the `#af_slice` parser function.
- Add the `#af_sort` parser function.
- Add the `#af_template` parser function.
- Add the `#af_unique` parser function.
- Add the `#af_unset` parser function.

[unreleased]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.17.0...HEAD
[v1.17.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.16.2...v1.17.0
[v1.16.2]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.16.1...v1.16.2
[v1.16.1]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.16.0...v1.16.1
[v1.16.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.15.0...v1.16.0
[v1.15.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.14.2...v1.15.0
[v1.14.2]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.14.1...v1.14.2
[v1.14.1]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.14.0...v1.14.1
[v1.14.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.13.0...v1.14.0
[v1.13.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.12.0...v1.13.0
[v1.12.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.11.0...v1.12.0
[v1.11.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.10.0...v1.11.0
[v1.10.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.9.0...v1.10.0
[v1.9.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.8.0...v1.9.0
[v1.8.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.7.0...v1.8.0
[v1.7.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.6.0...v1.7.0
[v1.6.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.5.0...v1.6.0
[v1.5.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.4.4...v1.5.0
[v1.4.4]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.4.3...v1.4.4
[v1.4.3]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.4.2...v1.4.3
[v1.4.2]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.4.1...v1.4.2
[v1.4.1]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.4.0...v1.4.1
[v1.4.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.3.0...v1.4.0
[v1.3.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.2.0...v1.3.0
[v1.2.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.1.0...v1.2.0
[v1.1.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.0.1...v1.1.0
[v1.0.1]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/wikimedia/mediawiki-extensions-ArrayFunctions/releases/tag/v1.0.0
