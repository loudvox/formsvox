# FormsVox — A VoiceCore Product: Submission & Manual Smoke Test Guide

## Core Plugin & SaaS Backend Repositories
- **FormsVox Free Core Plugin**: `https://github.com/loudvox/formsvox` (Public, GPLv2)
- **VoiceCore AI SaaS Service**: `https://github.com/loudvox/voicecore-ai` (Private)
- **FormsVox Pro Scaffold**: `https://github.com/loudvox/formsvox-pro` (Private)

---

## Step-by-Step Manual Smoke Test Instructions

### 1. Installation & Activation
1. Download or clone `formsvox` into `/wp-content/plugins/formsvox`.
2. Activate **FormsVox — Drag & Drop Form Builder** in WordPress Admin > Plugins.
3. Verify custom database tables `{prefix}formsvox_forms`, `{prefix}formsvox_entries`, and `{prefix}formsvox_entry_meta` are created.

### 2. Connect VoiceCore AI Service
1. Navigate to **FormsVox > Settings > VoiceCore AI Service**.
2. Input a valid VoiceCore API Key (`vc_live_...`).
3. Click **Save Settings**.
4. Click **Re-index Site Content Now** to index published pages/posts into VoiceCore `pgvector` store.

### 3. Build & Enable AI Mode on Form
1. Go to **FormsVox > Add New Form**.
2. Add fields: **Name** (`name_1`), **Email** (`email_1`), and **Message** (`message_1`).
3. In **Form Settings**, toggle **Conversational AI Mode** to **ON**.
4. Save the form and publish it to a WordPress page via shortcode `[formsvox id="1"]`.

### 4. Visitor Chat & Entry Verification
1. Visit the published form on the front-end.
2. Verify the **FormsVox Assistant — Powered by VoiceCore AI** chat widget appears.
3. Chat conversationally with the agent:
   - *"My name is Jane Smith"*
   - *"My email is jane@example.com"*
   - *"I need help with custom software development"*
4. The agent emits `submit_form`.
5. Verify the entry is recorded under **FormsVox > Entries** with:
   - Fields sanitized and populated (`Jane Smith`, `jane@example.com`).
   - Entry marked with the **AI** badge.
   - Click to inspect full transcript and lead qualification score.
   - Confirm email notification dispatch with `{all_fields}`.
