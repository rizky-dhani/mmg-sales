# Tasks: Add User Visit View

- [x] Create `VisitsRelationManager` for `UserResource` <!-- id: 0 -->
  - Define in `App\Filament\Resources\Users\RelationManagers\VisitsRelationManager`.
  - Configure the table to show relevant columns except the user's name.
- [x] Register `VisitsRelationManager` in `UserResource` <!-- id: 1 -->
  - Add to `getRelations()` array.
- [x] Add "Latest Visits" section to `UserInfolist` <!-- id: 2 -->
  - Use `RepeatableEntry` with a relationship limit of 5.
- [x] Verify functionality with a Pest test <!-- id: 3 -->
  - Assert that visits are visible on the User view page.