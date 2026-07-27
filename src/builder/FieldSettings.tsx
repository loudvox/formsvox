import React from 'react';
import { FieldConfig, ConditionalLogic } from '../types';

interface FieldSettingsProps {
  field: FieldConfig | null;
  allFields: FieldConfig[];
  onUpdateField: (updated: FieldConfig) => void;
  onDeleteField: (id: string) => void;
}

export const FieldSettings: React.FC<FieldSettingsProps> = ({
  field,
  allFields,
  onUpdateField,
  onDeleteField,
}) => {
  if (!field) {
    return <div className="formsvox-settings-empty">Select a field on the canvas to configure its properties.</div>;
  }

  const handleLogicToggle = (enabled: boolean) => {
    const logic: ConditionalLogic = field.conditional_logic || {
      enabled,
      action: 'show',
      match: 'all',
      rules: [],
    };
    logic.enabled = enabled;
    onUpdateField({ ...field, conditional_logic: logic });
  };

  const addRule = () => {
    const logic = { ...(field.conditional_logic || { enabled: true, action: 'show', match: 'all', rules: [] }) };
    const otherField = allFields.find((f) => f.id !== field.id);
    logic.rules.push({
      field_id: otherField ? otherField.id : '',
      operator: 'equals',
      value: '',
    });
    onUpdateField({ ...field, conditional_logic: logic });
  };

  return (
    <div className="formsvox-field-settings">
      <div className="formsvox-settings-header">
        <h3>Field Settings ({field.type})</h3>
        <button
          type="button"
          className="formsvox-btn-delete"
          onClick={() => onDeleteField(field.id)}
        >
          Delete Field
        </button>
      </div>

      <div className="formsvox-control-group">
        <label>Field Label</label>
        <input
          type="text"
          value={field.label}
          onChange={(e) => onUpdateField({ ...field, label: e.target.value })}
        />
      </div>

      <div className="formsvox-control-group">
        <label>Description / Help Text</label>
        <input
          type="text"
          value={field.description || ''}
          onChange={(e) => onUpdateField({ ...field, description: e.target.value })}
        />
      </div>

      <div className="formsvox-control-group">
        <label>Placeholder Text</label>
        <input
          type="text"
          value={field.placeholder || ''}
          onChange={(e) => onUpdateField({ ...field, placeholder: e.target.value })}
        />
      </div>

      <div className="formsvox-control-group checkbox">
        <label>
          <input
            type="checkbox"
            checked={!!field.required}
            onChange={(e) => onUpdateField({ ...field, required: e.target.checked })}
          />
          Required Field
        </label>
      </div>

      <div className="formsvox-control-group">
        <label>Custom CSS Class</label>
        <input
          type="text"
          value={field.css_class || ''}
          onChange={(e) => onUpdateField({ ...field, css_class: e.target.value })}
        />
      </div>

      {['select', 'radio', 'checkbox'].includes(field.type) && (
        <div className="formsvox-control-group">
          <label>Options (one per line)</label>
          <textarea
            rows={4}
            value={(field.options || []).map((o) => o.label).join('\n')}
            onChange={(e) => {
              const lines = e.target.value.split('\n');
              const opts = lines.map((l) => ({ label: l, value: l.toLowerCase().replace(/\s+/g, '_') }));
              onUpdateField({ ...field, options: opts });
            }}
          />
        </div>
      )}

      <hr />
      <h4>Conditional Logic (Free Feature)</h4>
      <div className="formsvox-control-group checkbox">
        <label>
          <input
            type="checkbox"
            checked={!!field.conditional_logic?.enabled}
            onChange={(e) => handleLogicToggle(e.target.checked)}
          />
          Enable Conditional Logic
        </label>
      </div>

      {field.conditional_logic?.enabled && (
        <div className="formsvox-logic-rules">
          <button type="button" className="button" onClick={addRule}>
            + Add Rule
          </button>
          {field.conditional_logic.rules.map((rule, idx) => (
            <div key={idx} className="formsvox-logic-rule-row">
              <span>If field</span>
              <select
                value={rule.field_id}
                onChange={(e) => {
                  const rules = [...field.conditional_logic!.rules];
                  rules[idx].field_id = e.target.value;
                  onUpdateField({
                    ...field,
                    conditional_logic: { ...field.conditional_logic!, rules },
                  });
                }}
              >
                {allFields
                  .filter((f) => f.id !== field.id)
                  .map((f) => (
                    <option key={f.id} value={f.id}>
                      {f.label} ({f.id})
                    </option>
                  ))}
              </select>
              <select
                value={rule.operator}
                onChange={(e) => {
                  const rules = [...field.conditional_logic!.rules];
                  rules[idx].operator = e.target.value as any;
                  onUpdateField({
                    ...field,
                    conditional_logic: { ...field.conditional_logic!, rules },
                  });
                }}
              >
                <option value="equals">equals</option>
                <option value="not_equals">does not equal</option>
                <option value="contains">contains</option>
              </select>
              <input
                type="text"
                value={rule.value}
                onChange={(e) => {
                  const rules = [...field.conditional_logic!.rules];
                  rules[idx].value = e.target.value;
                  onUpdateField({
                    ...field,
                    conditional_logic: { ...field.conditional_logic!, rules },
                  });
                }}
              />
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
