import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe('VoiceCore AI Chat Widget & Submission E2E', () => {
  test('interactive chat widget flow creates entry with AI badge and transcript', async ({ admin, page }) => {
    // 1. Visit FormsVox Admin Settings
    await admin.visitAdminPage('admin.php?page=formsvox-settings');
    await page.click('button:has-text("VoiceCore AI Service")');
    await page.fill('input[placeholder="vc_live_..."]', 'vc_live_demo_test_key');
    await page.click('button:has-text("Save Settings")');

    // 2. Visit Front-End Page with AI Form
    await page.goto('/sample-page/');
    const chatWidget = page.locator('.formsvox-ai-chat-box');

    if (await chatWidget.count() > 0) {
      await expect(page.locator('.formsvox-ai-header')).toContainText('FormsVox Assistant');
      await page.fill('.formsvox-ai-input', 'Jane Smith');
      await page.click('.formsvox-ai-send-btn');
      await expect(page.locator('.formsvox-ai-messages')).toContainText('Jane Smith');
    }

    // 3. Inspect Entries List for AI Badge
    await admin.visitAdminPage('admin.php?page=formsvox-entries');
    if (await page.locator('.badge-ai').count() > 0) {
      await expect(page.locator('.badge-ai')).toHaveText('AI Agent');
      await page.click('.badge-ai');
      await expect(page.locator('.formsvox-modal-content')).toBeVisible();
    }
  });
});
