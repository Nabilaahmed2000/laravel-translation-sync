# Changelog

All notable changes to `laravel-translation-sync` will be documented in this file.

## [2.0.0] - 2024-10-12

### ✨ Added
- **Automatic Translation Support**: Integration with Google Translate and other translation services
- **Multiple Translation Services**: Support for Google Translate, DeepL, Azure Translator, AWS Translate, and dummy service for testing
- **Enhanced Scanner Service**: 
  - Configurable scan paths and file extensions
  - Multiple translation pattern support
  - Context-aware detection showing file locations and code context
  - Translation statistics and coverage reports
- **Advanced Translation Writer**:
  - Automatic translation with fallback strategies
  - Support for both JSON and PHP translation file formats
  - Batch translation processing
  - Placeholder preservation for Laravel translation variables
- **Rich Console Interface**:
  - Enhanced command with multiple options (--translate, --auto, --dry-run, --stats)
  - Beautiful progress indicators and colored output
  - Detailed translation results and error reporting
  - Service configuration status display
- **Comprehensive Configuration**:
  - Flexible configuration file with environment variable support
  - Configurable translation patterns and scan paths
  - Multiple fallback strategies
  - Service-specific configuration options
- **New Commands**:
  - `translations:init` - Initialize translation files for specified locales
  - Enhanced `translations:sync` with advanced options
- **Developer Experience**:
  - Detailed documentation and examples
  - Environment configuration examples
  - Test examples
  - Error handling and validation

### 🔧 Enhanced
- **ScannerService**: Complete rewrite with better pattern detection and context awareness
- **TranslationWriter**: Enhanced with automatic translation capabilities and multiple file format support
- **Console Command**: Rich interface with progress indicators and detailed feedback
- **Service Provider**: Added configuration publishing and command registration

### 📚 Documentation
- Comprehensive README with usage examples
- Configuration documentation
- API examples and best practices
- Environment setup guide

### 🧪 Testing
- Added test examples for core functionality
- Service testing patterns
- Mock implementations for development

## [1.0.0] - 2024-XX-XX

### ✨ Initial Release
- Basic translation key detection in PHP and Blade files
- Simple translation file writing (JSON format only)
- Basic console command for manual translation management
- Laravel service provider integration

### Features
- Scan `app`, `resources`, and `routes` directories
- Detect `__()` and `@lang()` translation patterns
- Write missing translations to JSON files
- Interactive console prompts for translation approval