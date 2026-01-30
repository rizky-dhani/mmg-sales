# Tasks: Merge Visit into Activity

- [ ] Create migration to expand `activities` table and migrate data from `visits`.
- [ ] Update `Activity` model with new fields, relationships, and casts.
- [ ] Remove `Visit` model and its associated factory.
- [ ] Delete `Visit` migration files (after data is migrated).
- [ ] Update `ActivityResource` Schema (Form, Table, Infolist) to incorporate the unified fields.
- [ ] Implement conditional logic in `ActivityForm` to handle "Visit" specific fields.
- [ ] Port the `updateChecklist` action to `ActivityResource`.
- [ ] Delete `VisitResource` and all associated Filament pages/schemas.
- [ ] Update `Project` model relationships (activities will now cover what visits did).
- [ ] Update any dashboards or widgets that previously referenced `Visit`.
- [ ] Verify unified activity logging with tests.
