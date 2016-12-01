# Changelog
All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.0.3 

### Changed
- Make Nip_Collection extend Nip\Collection

## 0.0.2 

### Added
- AutoLoaderServiceProvider
- DispatcherServiceProvider
- RouterServiceProvider

### Changed
- Make Request extend Symfony Request

### Deprecated
- rename all Autoloader -> AutoLoader.
- rename Application dispatch() -> handleRequest().
- rename Application preDispatch() -> preHandleRequest().
- rename Application postDispatch() -> postDispatch().

### Removed
- FrontController

### Fixed
- Nothing.