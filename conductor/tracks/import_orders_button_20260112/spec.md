# Track Spec: Import Orders via Excel

## Overview
This track introduces the ability for Operations (Super Admin) and Regional Management (ASM/RSM) to bulk-import orders into the MMG Healthcare CRM using a fixed Excel (.xlsx) template. The process will be asynchronous and atomic to ensure system performance and data integrity.

## User Roles & Access
- **Operations (Super Admin):** Full access to download the template and perform imports.
- **Regional Management (ASM/RSM):** Access to download the template and perform imports for their respective regions.

## Functional Requirements
### 1. Template Management
- Provide a downloadable Excel (.xlsx) template with predefined headers matching the `orders` and `order_items` schema requirements.
- Headers should be user-friendly but map strictly to database fields (e.g., Customer, Product, Quantity, Order Date).

### 2. Import Action
- Add an "Import" button to the Orders resource list page in Filament.
- The button should open a modal for file upload.

### 3. Data Validation
- **Atomic Processing:** The entire import must fail if any single row contains a validation error.
- **Validation Rules:**
    - Required fields (Customer, Order Date, Items).
    - Data type validation (Dates, Numbers).
    - Existence checks (Customer ID/Code, Product ID/Code must exist in the database).
- **Duplicate Handling:** If an order (based on a unique identifier like Order Number) already exists, the row in the file is skipped, and the existing record is preserved.

### 4. Asynchronous Processing
- Files are uploaded and queued for background processing using Laravel's queue system.
- Users receive a Filament notification upon successful completion or failure.

## Non-Functional Requirements
- **Performance:** Handle files with up to 1,000 rows without impacting web server responsiveness.
- **Reliability:** Use database transactions to ensure "All or Nothing" atomicity.

## Acceptance Criteria
- [ ] Users with appropriate roles can see the "Import" button on the Orders list page.
- [ ] Users can download a correct `.xlsx` template.
- [ ] Uploading a valid file results in a background job that creates orders and line items.
- [ ] Uploading a file with a single invalid row (e.g., non-existent Product) results in no data being imported and an error notification.
- [ ] Uploading a file with a duplicate Order Number skips those rows and successfully imports the rest (if others are valid).
- [ ] A success notification is sent to the user when the background job finishes.

## Out of Scope
- Interactive column mapping (Fixed Template only).
- Support for CSV or other file formats.
- Partial imports (All or Nothing only).