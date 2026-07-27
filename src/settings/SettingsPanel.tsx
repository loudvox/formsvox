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
    voicecore_api_key: '',
    ai_transcript_retention: '30',
    delete_on_uninstall: false,
  });
  const [activeTab, setActiveTab] = useState<'general' | 'ai'>('general');
  const [saved, setSaved] = useState(false);
  const [syncStatus, setSyncStatus] = useState('');

  useEffect(() => {
    fetch('/wp-json/formsvox/v1/settings', {
      headers: { 'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '' },
    })
      .then((res) => res.json())
      .then((data) => {
        if (data) setSettings((prev) => ({ ...prev, ...data }));
      });
  }, []);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    await fetch('/wp-json/formsvox/v1/settings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '',
      },
      body: JSON.stringify(settings),
    });
    setSaved(true);
    setTimeout(() => setSaved(false), 2000);
  };

  const handleSyncContent = async () => {
    setSyncStatus('Re-indexing content...');
    try {
      const res = await fetch('/wp-json/formsvox/v1/ai/sync', {
        method: 'POST',
        headers: { 'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '' },
      });
      const data = await res.json();
      setSyncStatus(`Successfully indexed ${data.count || 0} pages/posts.`);
    } catch {
      setSyncStatus('Failed to sync content.');
    }
  };

  return (
    <div className="formsvox-settings-page">
      <h2>FormsVox Settings — A VoiceCore Product</h2>
      
      <div className="nav-tab-wrapper" style={{ marginBottom: '20px' }}>
        <button
          type="button"
          className={`nav-tab ${activeTab === 'general' ? 'nav-tab-active' : ''}`}
          onClick={() => setActiveTab('general')}
        >
          General & Integrations
        </button>
        <button
          type="button"
          className={`nav-tab ${activeTab === 'ai' ? 'nav-tab-active' : ''}`}
          onClick={() => setActiveTab('ai')}
        >
          VoiceCore AI Service
        </button>
      </div>

      {saved && <div className="notice notice-success inline"><p>Settings saved successfully.</p></div>}
      
      <form onSubmit={handleSave}>
        {activeTab === 'general' ? (
          <>
            <h3>Anti-Spam & Payment Credentials</h3>
            <table className="form-table">
              <tbody>
                <tr>
                  <th>reCAPTCHA Site Key</th>
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
              </tbody>
            </table>
          </>
        ) : (
          <>
            <h3>VoiceCore AI Platform Connection</h3>
            <div className="notice notice-info inline" style={{ marginBottom: '15px' }}>
              <p>
                <strong>Third-Party Service Disclosure:</strong> Connecting FormsVox to VoiceCore AI sends form schemas, visitor chat messages, and site content chunks (pages/posts/products) to the VoiceCore SaaS service (api.voicecore.ai) to process completions and answers. Data is strictly isolated per site.
              </p>
            </div>
            <table className="form-table">
              <tbody>
                <tr>
                  <th>VoiceCore API Key</th>
                  <td>
                    <input
                      type="password"
                      className="regular-text"
                      placeholder="vc_live_..."
                      value={settings.voicecore_api_key}
                      onChange={(e) => setSettings({ ...settings, voicecore_api_key: e.target.value })}
                    />
                    <p className="description">Issued from your VoiceCore Account dashboard (starts with <code>vc_</code>).</p>
                  </td>
                </tr>
                <tr>
                  <th>Transcript Retention</th>
                  <td>
                    <select
                      value={settings.ai_transcript_retention}
                      onChange={(e) => setSettings({ ...settings, ai_transcript_retention: e.target.value })}
                    >
                      <option value="7">7 Days</option>
                      <option value="30">30 Days</option>
                      <option value="90">90 Days</option>
                      <option value="365">1 Year</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>Site Content Indexing</th>
                  <td>
                    <button type="button" className="button button-secondary" onClick={handleSyncContent}>
                      Re-index Site Content Now
                    </button>
                    {syncStatus && <span style={{ marginLeft: '10px', color: '#2271b1' }}>{syncStatus}</span>}
                    <p className="description">Indexes published pages, posts, and products for conversational visitor Q&A.</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </>
        )}

        <p className="submit">
          <button type="submit" className="button button-primary">Save Settings</button>
        </p>
      </form>
    </div>
  );
};
