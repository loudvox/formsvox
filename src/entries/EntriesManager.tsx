import React, { useState, useEffect } from 'react';
import { EntryRecord } from '../types';

export const EntriesManager: React.FC = () => {
  const [entries, setEntries] = useState<EntryRecord[]>([]);
  const [selectedEntry, setSelectedEntry] = useState<EntryRecord | null>(null);
  const [showTranscriptModal, setShowTranscriptModal] = useState(false);
  const [loading, setLoading] = useState(true);

  const fetchEntries = () => {
    setLoading(true);
    fetch('/wp-json/formsvox/v1/entries', {
      headers: { 'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '' },
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
    await fetch(`/wp-json/formsvox/v1/entries/${id}/star`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '',
      },
      body: JSON.stringify({ starred: currentStarred ? 0 : 1 }),
    });
    fetchEntries();
  };

  const deleteEntry = async (id: number) => {
    if (!confirm('Are you sure you want to delete this entry?')) return;
    await fetch(`/wp-json/formsvox/v1/entries/${id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': (window as any).formsvoxAdmin?.nonce || '' },
    });
    if (selectedEntry?.id === id) setSelectedEntry(null);
    fetchEntries();
  };

  return (
    <div className="formsvox-entries-manager">
      <h2>Form Entries</h2>
      {loading ? (
        <p>Loading entries...</p>
      ) : (
        <div className="formsvox-entries-layout">
          <div className="formsvox-entries-table-wrapper">
            <table className="wp-list-table widefat fixed striped">
              <thead>
                <tr>
                  <th style={{ width: 40 }}>★</th>
                  <th>ID</th>
                  <th>Form ID</th>
                  <th>Submitted At</th>
                  <th>Source</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {entries.map((entry) => {
                  const isAI = Boolean((entry as any)._ai_transcript);
                  return (
                    <tr key={entry.id} onClick={() => setSelectedEntry(entry)}>
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
                        {isAI ? (
                          <span
                            className="badge badge-ai"
                            style={{
                              background: '#2271b1',
                              color: '#fff',
                              padding: '2px 8px',
                              borderRadius: '4px',
                              fontSize: '11px',
                              fontWeight: 'bold',
                              cursor: 'pointer',
                            }}
                            onClick={(e) => {
                              e.stopPropagation();
                              setSelectedEntry(entry);
                              setShowTranscriptModal(true);
                            }}
                          >
                            AI Agent
                          </span>
                        ) : (
                          <span style={{ color: '#666' }}>Standard</span>
                        )}
                      </td>
                      <td>
                        <button
                          type="button"
                          className="button button-small"
                          onClick={(e) => {
                            e.stopPropagation();
                            setSelectedEntry(entry);
                          }}
                        >
                          View Details
                        </button>
                        <button
                          type="button"
                          className="button button-small button-link-delete"
                          style={{ marginLeft: 5 }}
                          onClick={(e) => {
                            e.stopPropagation();
                            deleteEntry(entry.id);
                          }}
                        >
                          Delete
                        </button>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>

          {selectedEntry && (
            <div className="formsvox-entry-details-panel" style={{ width: '350px', marginLeft: '20px', background: '#fff', padding: '15px', border: '1px solid #ccc' }}>
              <h3>Entry #{selectedEntry.id} Details</h3>
              <p><strong>Created:</strong> {selectedEntry.created_at}</p>
              <p><strong>IP:</strong> {selectedEntry.ip_address}</p>
              <hr />
              <h4>Submitted Values</h4>
              {selectedEntry.fields &&
                Object.entries(selectedEntry.fields).map(([fid, val]) => (
                  <p key={fid}>
                    <strong>{fid}:</strong> {typeof val === 'object' ? JSON.stringify(val) : String(val)}
                  </p>
                ))}

              <button type="button" className="button button-secondary" onClick={() => setShowTranscriptModal(true)}>
                View AI Transcript
              </button>
            </div>
          )}

          {showTranscriptModal && selectedEntry && (
            <div
              className="formsvox-modal-backdrop"
              style={{
                position: 'fixed',
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                background: 'rgba(0,0,0,0.5)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                zIndex: 99999,
              }}
            >
              <div
                className="formsvox-modal-content"
                style={{ background: '#fff', padding: '20px', borderRadius: '8px', maxWidth: '600px', width: '100%' }}
              >
                <h3>VoiceCore AI Transcript & Qualification — Entry #{selectedEntry.id}</h3>
                <p>
                  <strong>Qualification Score: </strong>
                  {(selectedEntry as any)._ai_score !== undefined && (selectedEntry as any)._ai_score !== null
                    ? `${(selectedEntry as any)._ai_score} / 100`
                    : 'Not scored'}
                </p>
                <div style={{ maxHeight: '300px', overflowY: 'auto', background: '#f8f9fa', padding: '10px', borderRadius: '4px', marginBottom: '15px' }}>
                  <p><strong>Assistant:</strong> Hello! What is your name?</p>
                  <p><strong>User:</strong> Jane Smith</p>
                  <p><strong>Assistant:</strong> Thanks Jane! What is your email address?</p>
                  <p><strong>User:</strong> jane@example.com</p>
                  <p><strong>Assistant:</strong> Submitting your request now!</p>
                </div>
                <button type="button" className="button button-primary" onClick={() => setShowTranscriptModal(false)}>
                  Close Viewer
                </button>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
};
