# FormVox Releasing Guide & SVN Workflow

FormVox uses the official 10up `action-wordpress-plugin-deploy` GitHub Action to automate releases to the official WordPress.org SVN repository.

## GitHub Secrets Required
- `SVN_USERNAME`: Your WordPress.org username.
- `SVN_PASSWORD`: Your WordPress.org SVN password or App Password.

## Deployment Trigger
Releases are triggered automatically whenever a new Git tag (e.g. `v1.0.0`) is created and pushed to the `main` branch.

## Pre-Release Checklist
1. Update version number in `formvox.php` (`FORMVOX_VERSION`).
2. Update `readme.txt` header (`Stable tag: X.Y.Z`) and Changelog section.
3. Run test suite: `composer test` and `npm run test:e2e`.
4. Run static analysis and linting: `composer phpcs` and `composer phpstan`.
5. Run WordPress Plugin Check: ensure zero errors/warnings.
6. Commit changes: `git commit -m "chore(release): prepare release X.Y.Z"`.
7. Create tag and push:
   ```bash
   git tag v1.0.0
   git push origin main --tags
   ```
