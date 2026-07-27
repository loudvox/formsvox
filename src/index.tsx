import React, { useState } from 'react';
import { createRoot } from '@wordpress/element';
import { FormBuilder } from './builder/FormBuilder';
import { EntriesManager } from './entries/EntriesManager';
import { SettingsPanel } from './settings/SettingsPanel';

const App: React.FC = () => {
  const [view, setView] = useState<'builder' | 'entries' | 'settings'>('builder');
  const [activeFormId, setActiveFormId] = useState<number | null>(null);

  return (
    <div className="formsvox-admin-wrap">
      <nav className="formsvox-main-nav">
        <h2 className="formsvox-brand">FormsVox</h2>
        <button
          className={`button ${view === 'builder' ? 'button-primary' : ''}`}
          onClick={() => setView('builder')}
        >
          Form Builder
        </button>
        <button
          className={`button ${view === 'entries' ? 'button-primary' : ''}`}
          onClick={() => setView('entries')}
        >
          Entries
        </button>
        <button
          className={`button ${view === 'settings' ? 'button-primary' : ''}`}
          onClick={() => setView('settings')}
        >
          Settings
        </button>
      </nav>

      <main className="formsvox-admin-content">
        {view === 'builder' && <FormBuilder formId={activeFormId} />}
        {view === 'entries' && <EntriesManager />}
        {view === 'settings' && <SettingsPanel />}
      </main>
    </div>
  );
};

document.addEventListener('DOMContentLoaded', () => {
  const rootEl = document.getElementById('formsvox-admin-app');
  if (rootEl) {
    const root = createRoot(rootEl);
    root.render(<App />);
  }
});
