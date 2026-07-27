import React, { useState, useEffect } from 'react';

export const SettingsPanel: React.FC = () => {
  const [settings, setSettings] = useState({
    recaptcha_site_key: '',
    recaptcha_secret_key: '',
    turnstile_site_key: '',
    turnstile_secret_key: '',
    stripe_publishable: '',
    stripe_secret: '',
    mailchimp_api_key: '',
    delete_on_uninstall: false,
  });
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    fetch('/wp-json/formvox/v1/settings', {
      headers: { 'X-WP-Nonce': (window as any).formvoxAdmin?.nonce || '' },
    })
      .then((res) => res.json())
      .then((data) => {
        if (data) setSettings(data);
      });
  }, []);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    await fetch('/wp-json/formvox/v1/settings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': (window as any).formvoxAdmin?.nonce || '',
      },
      body: JSON.stringify(settings),
    });
    setSaved(true);
    setTimeout(() => setSaved(false), 2000);
  };

  return (
    <div className="formvox-settings-page">
      <h2>FormVox Settings</h2>
      {saved && <div className="notice notice-success inline"><p>Settings saved successfully.</p></div>}
      <form onSubmit={handleSave}>
        <h3>Anti-Spam Credentials</h3>
        <table className="form-table">
          <tbody>
            <tr>
              <th>reCAPTCHA v2/v3 Site Key</th>
              <td>
                <input
                  type="text"
                  className="regular-text"
                  value={settings.recaptcha_site_key}
                  onChange={(e) => setSettings({ ...settings, recaptcha_site_key: e.target.value })}
                />
              </td>
            </tr>
            <tr>
              <th>Cloudflare Turnstile Site Key</th>
              <td>
                <input
                  type="text"
                  className="regular-text"
                  value={settings.turnstile_site_key}
                  onChange={(e) => setSettings({ ...settings, turnstile_site_key: e.target.value })}
                />
              </td>
            </tr>
          </tbody>
        </table>

        <h3>Payment & Marketing Integrations</h3>
        <table className="form-table">
          <tbody>
            <tr>
              <th>Stripe Publishable Key</th>
              <td>
                <input
                  type="text"
                  className="regular-text"
                  value={settings.stripe_publishable}
                  onChange={(e) => setSettings({ ...settings, stripe_publishable: e.target.value })}
                />
              </td>
            </tr>
            <tr>
              <th>Mailchimp API Key</th>
              <td>
                <input
                  type="text"
                  className="regular-text"
                  value={settings.mailchimp_api_key}
                  onChange={(e) => setSettings({ ...settings, mailchimp_api_key: e.target.value })}
                />
              </td>
            </tr>
          </tbody>
        </table>

        <h3>Uninstall & Data Management</h3>
        <table className="form-table">
          <tbody>
            <tr>
              <th>Delete Data on Uninstall</th>
              <td>
                <label>
                  <input
                    type="checkbox"
                    checked={settings.delete_on_uninstall}
                    onChange={(e) => setSettings({ ...settings, delete_on_uninstall: e.target.checked })}
                  />
                  Remove all FormVox forms, entries, and options upon plugin deletion.
                </label>
              </td>
            </tr>
          </tbody>
        </table>

        <button type="submit" className="button button-primary">Save Settings</button>
      </form>
    </div>
  );
};
