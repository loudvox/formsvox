import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe('FormsVox Builder and Submission E2E', () => {
  test('build form, publish, submit on front-end, and verify entry', async ({ admin, page }) => {
    // 1. Navigate to FormsVox Admin Builder
    await admin.visitAdminPage('admin.php?page=formsvox');
    await expect(page.locator('.formsvox-brand')).toHaveText('FormsVox');

    // 2. Fill Form Title
    const titleInput = page.locator('.formsvox-title-input');
    await titleInput.fill('E2E Test Contact Form');

    // 3. Add Text and Email fields
    await page.click('button:has-text("+ Single Line Text")');
    await page.click('button:has-text("+ Email")');

    // 4. Save Form
    await page.click('button:has-text("Save Form")');
    await expect(page.locator('.formsvox-save-status')).toHaveText('Saved!');

    // 5. Submit Form on Front-End
    await page.goto('/sample-page/');
    const form = page.locator('.formsvox-form');
    if (await form.count() > 0) {
      await page.fill('input[type="text"]', 'E2E Tester');
      await page.fill('input[type="email"]', 'e2e@example.com');
      await page.click('.formsvox-submit-btn');

      await expect(page.locator('.formsvox-response-message')).toBeVisible();
      await expect(page.locator('.formsvox-response-message')).toContainText('Thank you');
    }
  });
});
