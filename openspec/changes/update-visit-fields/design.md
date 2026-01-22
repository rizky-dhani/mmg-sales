# Design: Update Visit Fields and Form

## Database Schema
The `visits` table will be updated with the following fields:
-   `visit_type`: `ENUM('in_person', 'video_call', 'phone_call', 'messaging')`
-   `meeting_link`: `VARCHAR(255)` (NULLable)
-   `messaging_platform`: `VARCHAR(50)` (NULLable - e.g., WhatsApp, Email, etc.)
-   `confidence_level`: `INTEGER` (0-100, default 50)

## Model Changes
The `Visit` model will include the new fields in `$fillable` and appropriate casting for `confidence_level`.

## UI/UX Design (Filament)
### Visit Form
-   **Visit Type**: A required `Select` component.
-   **Conditional Fields**:
    -   `meeting_link` will be visible only when `visit_type` is `video_call`.
    -   `messaging_platform` will be visible only when `visit_type` is `messaging`.
-   **Confidence Level**: A `Slider` component:
    -   `min(0)`
    -   `max(100)`
    -   `step(5)`
    -   `tooltips()`
-   **Relationships**: `customer_id` and `contact_id` are already using `Select` with `relationship()`. I will ensure they are reactive (`live()`) to support conditional logic if needed (e.g., filtering contacts by customer).

## Alternatives Considered
-   Using a separate table for visit types: Overkill for a simple classification.
-   Using a free-text field for confidence: Harder to analyze quantitatively.
