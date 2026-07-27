import React, { useState, useEffect } from 'react';
import { EntryRecord } from '../types';

export const EntriesManager: React.FC = () => {
  const [entries, setEntries] = useState<EntryRecord[]>([]);
  const [selectedEntry, setSelectedEntry] = useState<EntryRecord | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchEntries = () => {
    setLoading(true);
    fetch('/wp-json/formvox/v1/entries', {
      headers: { 'X-WP-Nonce': (window as any).formvoxAdmin?.nonce || '' },
    })
      .then((res) => res.json())
      .then((data) => {
        if (data && data.items) {
          setEntries(data.items);
        }
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchEntries();
  }, []);

  const toggleStar = async (id: number, currentStarred: number) => {
    await fetch(`/wp-json/formvox/v1/entries/${id}/star`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': (window as any).formvoxAdmin?.nonce || '',
      },
      body: JSON.stringify({ starred: currentStarred ? 0 : 1 }),
    });
    fetchEntries();
  };

  const deleteEntry = async (id: number) => {
    if (!confirm('Are you sure you want to delete this entry?')) return;
    await fetch(`/wp-json/formvox/v1/entries/${id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': (window as any).formvoxAdmin?.nonce || '' },
    });
    if (selectedEntry?.id === id) setSelectedEntry(null);
    fetchEntries();
  };

  return (
    <div className="formvox-entries-manager">
      <h2>Form Entries</h2>
      {loading ? (
        <p>Loading entries...</p>
      ) : (
        <div className="formvox-entries-layout">
          <div className="formvox-entries-table-wrapper">
            <table className="wp-list-table widefat fixed striped">
              <thead>
                <tr>
                  <th style={{ width: 40 }}>★</th>
                  <th>ID</th>
                  <th>Form ID</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {entries.map((entry) => (
                  <tr key={entry.id} onClick={() => setSelectedEntry(entry)} style={{ cursor: 'pointer' }}>
                    <td>
                      <button
                        type="button"
                        className="button-link"
                        onClick={(e) => {
                          e.stopPropagation();
                          toggleStar(entry.id, entry.starred);
                        }}
                      >
                        {entry.starred ? '★' : '☆'}
                      </button>
                    </td>
                    <td>#{entry.id}</td>
                    <td>{entry.form_id}</td>
                    <td>{entry.created_at}</td>
                    <td>
                      <button
                        type="button"
                        className="button button-small button-link-delete"
                        onClick={(e) => {
                          e.stopPropagation();
                          deleteEntry(entry.id);
                        }}
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {selectedEntry && (
            <aside className="formvox-entry-detail-card">
              <h3>Entry #{selectedEntry.id} Details</h3>
              <p>
                <strong>Date:</strong> {selectedEntry.created_at}
              </p>
              <p>
                <strong>IP:</strong> {selectedEntry.ip_address}
              </p>
              <hr />
              <h4>Field Responses</h4>
              {Object.entries(selectedEntry.fields || {}).map(([key, val]) => (
                <div key={key} style={{ marginBottom: 10 }}>
                  <strong>{key}:</strong>{' '}
                  <span>{typeof val === 'object' ? JSON.stringify(val) : String(val)}</span>
                </div>
              ))}
            </aside>
          )}
        </div>
      )}
    </div>
  );
};
