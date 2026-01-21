# General Guidelines

- Don't include any superfluous PHP Annotations, except ones that start with `@` for typing variables.
- Use DTOs for passing structured data to inertia views.
- Always validate and sanitize user inputs.
- Use kebbab-case for file names and directories in js and inertia page.
- Add soft deletes to models where applicable.
- Use date-fns for date manipulations in typescript.
- Always use the css variables defined in the project for colors, spacing, fonts, etc instead of utility class.
- Use existing Roles and Permissions in tests instead of creating new ones. Check `database/seeders/RolePermissionSeeder.php` for existing roles and permissions.
- Check the `docs/` directory for the plan, architecture, and other relevant documentation before starting a new feature.
