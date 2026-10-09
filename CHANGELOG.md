# Changelog

Notable changes are documented in this file

---
## [3.0.0] - 2026-10-09
> [!WARNING]
> **Major Release (Breaking Changes):** This version has breaking changes from previous v1.x
### ⚠️ Breaking Changes
- **Akka dependency:** This plugin now requires the Wordpress plugin Akka Headless Wordpress >= v3 to be active
- **Namespace and Class names:** Previous global classes (such as `Akka_headless_wp_forms_block`) are now namespaced and renamed (like `\AkkaForms\Block`).
- **Hook names:** Filters and actions previously prefixes with `ahw_forms_` are now prefixed with `akka_forms_`.
### ✨ Refactoring
- File names in plugin previously prefixed with `ahw-forms-` are now prefixed with `akka-forms-`.
