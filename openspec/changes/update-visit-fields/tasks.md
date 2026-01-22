# Tasks: Update Visit Fields and Form

## Database & Model
- [x] Create a migration to add `visit_type`, `meeting_link`, `messaging_platform`, and `confidence_level` to the `visits` table.
- [x] Update the `Visit` model `$fillable` and `$casts`.

## Filament UI
- [x] Update `VisitForm.php` to include the `visit_type` select field.
- [x] Implement conditional visibility for `meeting_link` and `messaging_platform`.
- [x] Implement the `confidence_level` slider with required configuration (steps, tooltips, range).
- [x] Verify that relationship fields use Select dropdowns and load data correctly.

## Verification
- [x] Run migrations and seeders.
- [x] Manually verify the form behavior in the browser.
- [x] Run existing tests to ensure no regressions.