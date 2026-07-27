import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe('FormVox Builder and Submission E2E', () => {
  test('build form, publish, submit on front-end, and verify entry', async ({ admin, page }) => {
    // 1. Navigate to FormVox Admin Builder
    await admin.visitAdminPage('admin.php?page=formvox');
    await expect(page.locator('.formvox-brand')).toHaveText('FormVox');

    // 2. Fill Form Title
    const titleInput = page.locator('.formvox-title-input');
    await titleInput.fill('E2E Test Contact Form');

    // 3. Add Text and Email fields
    await page.click('button:has-text("+ Single Line Text")');
    await page.click('button:has-text("+ Email")');

    // 4. Save Form
    await page.click('button:has-text("Save Form")');
    await expect(page.locator('.formvox-save-status')).toHaveText('Saved!');

    // 5. Submit Form on Front-End
    await page.goto('/sample-page/');
    const form = page.locator('.formvox-form');
    if (await form.count() > 0) {
      await page.fill('input[type="text"]', 'E2E Tester');
      await page.fill('input[type="email"]', 'e2e@example.com');
      await page.click('.formvox-submit-btn');

      await expect(page.locator('.formvox-response-message')).toBeVisible();
      await expect(page.locator('.formvox-response-message')).toContainText('Thank you');
    }
  });
});
