import React from 'react';
import { FieldConfig } from '../types';

interface FieldPaletteProps {
  onAddField: (type: string, label: string) => void;
}

const PALETTE_ITEMS = [
  { type: 'text', label: 'Single Line Text', category: 'Standard' },
  { type: 'textarea', label: 'Paragraph Text', category: 'Standard' },
  { type: 'name', label: 'Name', category: 'Standard' },
  { type: 'email', label: 'Email', category: 'Standard' },
  { type: 'phone', label: 'Phone', category: 'Standard' },
  { type: 'address', label: 'Address', category: 'Standard' },
  { type: 'url', label: 'Website / URL', category: 'Standard' },
  { type: 'number', label: 'Number', category: 'Standard' },
  { type: 'slider', label: 'Number Slider', category: 'Fancy' },
  { type: 'select', label: 'Dropdown', category: 'Standard' },
  { type: 'radio', label: 'Multiple Choice', category: 'Standard' },
  { type: 'checkbox', label: 'Checkboxes', category: 'Standard' },
  { type: 'date_time', label: 'Date / Time', category: 'Standard' },
  { type: 'file_upload', label: 'File Upload', category: 'Fancy' },
  { type: 'password', label: 'Password', category: 'Fancy' },
  { type: 'hidden', label: 'Hidden Field', category: 'Fancy' },
  { type: 'page_break', label: 'Page Break', category: 'Layout' },
  { type: 'section', label: 'Section Divider', category: 'Layout' },
  { type: 'html', label: 'HTML / Content', category: 'Layout' },
  { type: 'rating', label: 'Star Rating', category: 'Fancy' },
  { type: 'likert', label: 'Likert Scale', category: 'Fancy' },
  { type: 'nps', label: 'NPS Score', category: 'Fancy' },
  { type: 'layout', label: 'Columns / Layout', category: 'Layout' },
  { type: 'repeater', label: 'Repeater', category: 'Fancy' },
  { type: 'payment_single', label: 'Single Item', category: 'Payment' },
  { type: 'payment_multiple', label: 'Multiple Items', category: 'Payment' },
  { type: 'payment_total', label: 'Total Price', category: 'Payment' },
];

export const FieldPalette: React.FC<FieldPaletteProps> = ({ onAddField }) => {
  const categories = ['Standard', 'Fancy', 'Layout', 'Payment'];

  return (
    <div className="formvox-palette">
      <h3>Add Fields</h3>
      {categories.map((cat) => (
        <div key={cat} className="formvox-palette-category">
          <h4>{cat} Fields</h4>
          <div className="formvox-palette-grid">
            {PALETTE_ITEMS.filter((item) => item.category === cat).map((item) => (
              <button
                key={item.type}
                type="button"
                className="formvox-palette-btn"
                onClick={() => onAddField(item.type, item.label)}
              >
                + {item.label}
              </button>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
};
