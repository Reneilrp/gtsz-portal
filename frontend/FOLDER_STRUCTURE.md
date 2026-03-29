# AccommoTrackWeb Folder Structure Audit

This document outlines the source code organization of the AccommoTrackWeb project and identifies components suitable for cross-platform sharing with AccommoTrackMobile.

## 📂 Directory Overview

### `src/services/` (High Sharing Potential)
Contains the API client logic for communicating with the Laravel backend.
- **Usage:** Centralizes all `GET/POST/PUT/DELETE` requests.
- **Sharing Status:** 🟡 **Partial.** The logic is platform-agnostic, but they currently import a web-specific `api.js` instance.
- **Key Files:** `bookingService.js`, `propertyService.js`, `authService.js`.

### `src/utils/` (Mixed Potential)
Helper functions for data transformation and environment handling.
- **`propertyHelpers.js`:** 🟢 **High.** Maps raw backend JSON to UI-friendly objects. Pure logic.
- **`price.js`:** 🟢 **High.** Currency and number formatting.
- **`api.js`:** 🔴 **Low.** Web-specific (uses `localStorage` and `window.location`).
- **`imageUtils.js`:** 🟡 **Medium.** Uses `import.meta.env`. Logic is shared, but environment access differs on Mobile.

### `src/shared/` (Immediate Sharing)
Static content and configuration used by multiple parts of the app.
- **Usage:** Legal documents (T&C, Privacy Policy) and notification settings.
- **Sharing Status:** 🟢 **High.** Currently duplicated manually between Web and Mobile.

### `src/contexts/` (Low Sharing Potential)
React Context providers for state management.
- **Usage:** Manages UI states like Sidebar visibility and user preferences.
- **Sharing Status:** 🔴 **Low.** Tightly coupled to React DOM and Web-specific UI flows.

### `src/components/` & `src/screens/` (Platform Specific)
The UI layer built with Tailwind CSS and React DOM components.
- **Usage:** Visual presentation organized by user role (Admin, Landlord, Tenant, Guest).
- **Sharing Status:** 🔴 **None.** Mobile uses React Native primitives (`View`, `Text`) and cannot use these components.

### `src/Navigation/` (Platform Specific)
Web-specific routing logic using `react-router-dom`.

---

## 🛠 Integration Strategy for Other Projects

To use this structure from `AccommoTrackMobile`, the following steps are recommended:

1.  **Decouple API Client:** Move the base axios configuration in `utils/api.js` to a factory pattern that accepts a `storage` provider (e.g., `localStorage` for Web, `AsyncStorage` for Mobile).
2.  **Standardize Environment Access:** Use a shared config utility instead of direct `import.meta.env` or `process.env` calls.
3.  **Unified Model Mapping:** Centralize `propertyHelpers.js` so that both platforms interpret backend data (room types, prices, statuses) identically.
