import React, { useState, useEffect } from 'react';
import { FieldPalette } from './FieldPalette';
import { FieldSettings } from './FieldSettings';
import { FormSettings } from './FormSettings';
import { FormSchema, FieldConfig, FormRecord } from '../types';

interface FormBuilderProps {
  formId: number | null;
  onSaveSuccess?: () => void;
}

export const FormBuilder: React.FC<FormBuilderProps> = ({ formId, onSaveSuccess }) => {
  const [schema, setSchema] = useState<FormSchema>({
    fields: [],
    settings: {
      title: 'New Form',
      description: '',
      submit_text: 'Submit',
      ajax_submit: true,
    },
    notifications: [
      {
        id: 'notif_1',
        name: 'Admin Notification',
        to_email: '{admin_email}',
        subject: 'New Submission from {form_name}',
        message: '{all_fields}',
      },
    ],
    confirmations: [
      {
        id: 'conf_1',
        type: 'message',
        message: 'Thank you! Your form has been submitted successfully.',
      },
    ],
  });

  const [selectedFieldId, setSelectedFieldId] = useState<string | null>(null);
  const [activeTab, setActiveTab] = useState<'fields' | 'settings' | 'preview'>('fields');
  const [isSaving, setIsSaving] = useState(false);
  const [saveStatus, setSaveStatus] = useState<string>('');

  useEffect(() => {
    if (formId) {
      fetch(`/wp-json/formsvox/v1/forms/${formId}`, {
        headers: { 'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '' },
      })
        .then((res) => res.json())
        .then((data: FormRecord) => {
          if (data && data.schema) {
            setSchema(data.schema);
          }
        });
    }
  }, [formId]);

  const handleAddField = (type: string, label: string) => {
    const id = `field_${type}_${Date.now()}`;
    const newField: FieldConfig = {
      id,
      type,
      label,
      required: false,
      options: ['select', 'radio', 'checkbox'].includes(type)
        ? [
            { label: 'Option 1', value: 'option_1' },
            { label: 'Option 2', value: 'option_2' },
          ]
        : undefined,
    };
    setSchema({
      ...schema,
      fields: [...schema.fields, newField],
    });
    setSelectedFieldId(id);
  };

  const handleUpdateField = (updated: FieldConfig) => {
    setSchema({
      ...schema,
      fields: schema.fields.map((f) => (f.id === updated.id ? updated : f)),
    });
  };

  const handleDeleteField = (id: string) => {
    setSchema({
      ...schema,
      fields: schema.fields.filter((f) => f.id !== id),
    });
    if (selectedFieldId === id) {
      setSelectedFieldId(null);
    }
  };

  const handleSave = async () => {
    setIsSaving(true);
    setSaveStatus('Saving...');
    const url = formId ? `/wp-json/formsvox/v1/forms/${formId}` : '/wp-json/formsvox/v1/forms';
    const method = formId ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '',
        },
        body: JSON.stringify({
          title: schema.settings.title,
          schema: schema,
        }),
      });
      const data = await res.json();
      setSaveStatus('Saved!');
      setTimeout(() => setSaveStatus(''), 2000);
      if (onSaveSuccess) onSaveSuccess();
    } catch (err) {
      setSaveStatus('Error saving form');
    } finally {
      setIsSaving(false);
    }
  };

  const selectedField = schema.fields.find((f) => f.id === selectedFieldId) || null;

  return (
    <div className="formsvox-builder-container">
      <header className="formsvox-builder-header">
        <input
          type="text"
          className="formsvox-title-input"
          value={schema.settings.title}
          onChange={(e) => setSchema({ ...schema, settings: { ...schema.settings, title: e.target.value } })}
        />
        <div className="formsvox-header-nav">
          <button
            className={`button ${activeTab === 'fields' ? 'button-primary' : ''}`}
            onClick={() => setActiveTab('fields')}
          >
            Fields
          </button>
          <button
            className={`button ${activeTab === 'settings' ? 'button-primary' : ''}`}
            onClick={() => setActiveTab('settings')}
          >
            Form Settings
          </button>
          <button
            className={`button ${activeTab === 'preview' ? 'button-primary' : ''}`}
            onClick={() => setActiveTab('preview')}
          >
            Live Preview
          </button>
        </div>
        <div className="formsvox-header-actions">
          {saveStatus && <span className="formsvox-save-status">{saveStatus}</span>}
          <button type="button" className="button button-primary button-large" onClick={handleSave} disabled={isSaving}>
            {isSaving ? 'Saving...' : 'Save Form'}
          </button>
        </div>
      </header>

      <div className="formsvox-builder-body">
        {activeTab === 'fields' && (
          <>
            <FieldPalette onAddField={handleAddField} />
            <main className="formsvox-canvas">
              <h3>Form Canvas</h3>
              {schema.fields.length === 0 ? (
                <div className="formsvox-canvas-empty">
                  Click fields on the left palette to add them to your form.
                </div>
              ) : (
                schema.fields.map((field) => (
                  <div
                    key={field.id}
                    className={`formsvox-canvas-item ${selectedFieldId === field.id ? 'selected' : ''}`}
                    onClick={() => setSelectedFieldId(field.id)}
                  >
                    <div className="formsvox-canvas-item-label">
                      {field.label} {field.required && <span style={{ color: 'red' }}>*</span>}
                    </div>
                    <div className="formsvox-canvas-item-type">{field.type}</div>
                  </div>
                ))
              )}
            </main>
            <aside className="formsvox-sidebar">
              <FieldSettings
                field={selectedField}
                allFields={schema.fields}
                onUpdateField={handleUpdateField}
                onDeleteField={handleDeleteField}
              />
            </aside>
          </>
        )}

        {activeTab === 'settings' && (
          <FormSettings schema={schema} onUpdateSchema={setSchema} />
        )}

        {activeTab === 'preview' && (
          <div className="formsvox-live-preview">
            <h3>Live Preview</h3>
            <form onSubmit={(e) => e.preventDefault()}>
              {schema.fields.map((f) => (
                <div key={f.id} style={{ marginBottom: 15 }}>
                  <label style={{ display: 'block', fontWeight: 'bold' }}>{f.label}</label>
                  <input type="text" placeholder={f.placeholder} style={{ width: '100%', padding: 8 }} readOnly />
                </div>
              ))}
              <button type="button" className="button button-primary">
                {schema.settings.submit_text}
              </button>
            </form>
          </div>
        )}
      </div>
    </div>
  );
};
