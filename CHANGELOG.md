# Changelog
All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.0
### Deprecated
- rename all occurrences of DATE_DB

## 0.9.3 

### Deprecated
- rename all Nip_Locale -> Nip\locale()

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