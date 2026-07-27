import React, { useState } from 'react';
import { FormSchema, NotificationConfig, ConfirmationConfig } from '../types';

interface FormSettingsProps {
  schema: FormSchema;
  onUpdateSchema: (updated: FormSchema) => void;
}

export const FormSettings: React.FC<FormSettingsProps> = ({ schema, onUpdateSchema }) => {
  const [activeTab, setActiveTab] = useState<'general' | 'notifications' | 'confirmations'>('general');

  const updateGeneral = (key: string, value: any) => {
    onUpdateSchema({
      ...schema,
      settings: {
        ...schema.settings,
        [key]: value,
      },
    });
  };

  const addNotification = () => {
    const notifs = [...schema.notifications];
    notifs.push({
      id: `notif_${Date.now()}`,
      name: `Notification ${notifs.length + 1}`,
      to_email: '{admin_email}',
      subject: 'New Form Submission #{entry_id}',
      message: '{all_fields}',
    });
    onUpdateSchema({ ...schema, notifications: notifs });
  };

  return (
    <div className="formvox-form-settings-panel">
      <div className="formvox-sub-tabs">
        <button
          className={activeTab === 'general' ? 'active' : ''}
          onClick={() => setActiveTab('general')}
        >
          General
        </button>
        <button
          className={activeTab === 'notifications' ? 'active' : ''}
          onClick={() => setActiveTab('notifications')}
        >
          Notifications ({schema.notifications.length})
        </button>
        <button
          className={activeTab === 'confirmations' ? 'active' : ''}
          onClick={() => setActiveTab('confirmations')}
        >
          Confirmations ({schema.confirmations.length})
        </button>
      </div>

      {activeTab === 'general' && (
        <div className="formvox-tab-content">
          <div className="formvox-control-group">
            <label>Form Title</label>
            <input
              type="text"
              value={schema.settings.title}
              onChange={(e) => updateGeneral('title', e.target.value)}
            />
          </div>
          <div className="formvox-control-group">
            <label>Form Description</label>
            <textarea
              rows={3}
              value={schema.settings.description}
              onChange={(e) => updateGeneral('description', e.target.value)}
            />
          </div>
          <div className="formvox-control-group">
            <label>Submit Button Text</label>
            <input
              type="text"
              value={schema.settings.submit_text}
              onChange={(e) => updateGeneral('submit_text', e.target.value)}
            />
          </div>
          <div className="formvox-control-group checkbox">
            <label>
              <input
                type="checkbox"
                checked={schema.settings.ajax_submit}
                onChange={(e) => updateGeneral('ajax_submit', e.target.checked)}
              />
              Enable AJAX Form Submission (No Page Reload)
            </label>
          </div>
        </div>
      )}

      {activeTab === 'notifications' && (
        <div className="formvox-tab-content">
          <button type="button" className="button" onClick={addNotification}>
            + Add Notification Email
          </button>
          {schema.notifications.map((n, idx) => (
            <div key={n.id} className="formvox-card">
              <h4>{n.name}</h4>
              <div className="formvox-control-group">
                <label>Send To Email</label>
                <input
                  type="text"
                  value={n.to_email}
                  onChange={(e) => {
                    const notifs = [...schema.notifications];
                    notifs[idx].to_email = e.target.value;
                    onUpdateSchema({ ...schema, notifications: notifs });
                  }}
                />
              </div>
              <div className="formvox-control-group">
                <label>Subject</label>
                <input
                  type="text"
                  value={n.subject}
                  onChange={(e) => {
                    const notifs = [...schema.notifications];
                    notifs[idx].subject = e.target.value;
                    onUpdateSchema({ ...schema, notifications: notifs });
                  }}
                />
              </div>
              <div className="formvox-control-group">
                <label>Email Body (Smart Tags Supported: {'{all_fields}'}, {'{entry_id}'}, {'{field_id="id"}'})</label>
                <textarea
                  rows={4}
                  value={n.message}
                  onChange={(e) => {
                    const notifs = [...schema.notifications];
                    notifs[idx].message = e.target.value;
                    onUpdateSchema({ ...schema, notifications: notifs });
                  }}
                />
              </div>
            </div>
          ))}
        </div>
      )}

      {activeTab === 'confirmations' && (
        <div className="formvox-tab-content">
          {schema.confirmations.map((c, idx) => (
            <div key={c.id || idx} className="formvox-card">
              <h4>Confirmation</h4>
              <div className="formvox-control-group">
                <label>Confirmation Type</label>
                <select
                  value={c.type}
                  onChange={(e) => {
                    const confs = [...schema.confirmations];
                    confs[idx].type = e.target.value as any;
                    onUpdateSchema({ ...schema, confirmations: confs });
                  }}
                >
                  <option value="message">Show Message</option>
                  <option value="redirect">Redirect URL</option>
                </select>
              </div>
              {c.type === 'message' && (
                <div className="formvox-control-group">
                  <label>Message Content</label>
                  <textarea
                    rows={3}
                    value={c.message || ''}
                    onChange={(e) => {
                      const confs = [...schema.confirmations];
                      confs[idx].message = e.target.value;
                      onUpdateSchema({ ...schema, confirmations: confs });
                    }}
                  />
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
