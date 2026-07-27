# FormsVox WordPress.org Submission Guide

This document summarizes the exact steps required to submit `formsvox.zip` to the WordPress.org plugin review queue.

---

## 1. Pre-Submission Build & Zip Packaging

Run the build script to compile assets and generate the production distribution zip:

```bash
# 1. Install production PHP dependencies
composer install --no-dev --optimize-autoloader

# 2. Build React admin builder assets
npm run build

# 3. Create clean zip file excluding developer files
zip -r formsvox.zip . \
  -x "*.git*" \
  -x "*node_modules*" \
  -x "*tests*" \
  -x "*.github*" \
  -x "composer.phar" \
  -x "phpcs.xml.dist" \
  -x "phpstan.neon" \
  -x "phpunit.xml.dist" \
  -x "tsconfig.json"
```

---

## 2. WordPress.org Plugin Review Queue Submission Steps

1. **Log in to WordPress.org**: Go to [https://wordpress.org/plugins/developers/add/](https://wordpress.org/plugins/developers/add/).
2. **Upload `formsvox.zip`**: Select the generated `formsvox.zip` file.
3. **Review Plugin Slug**: Confirm the plugin slug is `formsvox`.
4. **Submit for Review**: Click **Submit Form**. The WordPress.org Plugin Review Team will analyze the plugin source code.
5. **SVN Repository Access**: Once approved, you will receive an email containing SVN repository write access credentials (`https://plugins.svn.wordpress.org/formsvox/`).

---

## 3. Initial SVN Tag & Release Deployment

Once approved:
1. Checkout the SVN repository:
   ```bash
   svn co https://plugins.svn.wordpress.org/formsvox/ svn-formsvox
   ```
2. Copy plugin files to `svn-formsvox/trunk/` and assets to `svn-formsvox/assets/`.
3. Create the `1.0.0` tag:
   ```bash
   cp -r svn-formsvox/trunk svn-formsvox/tags/1.0.0
   ```
4. Commit to SVN:
   ```bash
   cd svn-formsvox
   svn add --force trunk/ tags/1.0.0/ assets/
   svn commit -m "Tagging release 1.0.0"
   ```
5. Subsequent releases will deploy automatically via the GitHub Action defined in `.github/workflows/ci.yml`!
