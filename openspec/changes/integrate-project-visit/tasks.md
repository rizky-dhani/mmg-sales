# Tasks: Integrate Project with Visit

- [ ] Create migration to add `project_id` to `visits` table and `visit_id` to `activities` table.
- [ ] Update `Visit` model with `project()` relationship and `$fillable` attribute.
- [ ] Update `Activity` model with `visit()` relationship and `$fillable` attribute.
- [ ] Update `VisitForm` in `app/Filament/Resources/Visits/Schemas/VisitForm.php` to include `project_id` field.
- [ ] Register `VisitObserver` in `AppServiceProvider`.
- [ ] Implement `VisitObserver` to synchronize visits with project activities.
- [ ] Add `updateChecklist` action to `VisitResource` (table and pages).
- [ ] Verify integration with tests.
