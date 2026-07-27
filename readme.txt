=== FormVox - Drag & Drop Form Builder ===
Contributors: loudvox
Tags: form builder, contact form, forms, stripe, mailchimp
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

FormVox is the fast, accessible, developer-friendly WordPress form builder plugin with drag & drop builder, 27 field types, conditional logic, entries, Stripe payments, and free Webhooks.

== Description ==

FormVox provides a modern, fast, and accessible drag-and-drop form building experience for WordPress site owners and developers. Unlike other plugins that hide essential features behind high per-site subscription paywalls, FormVox includes conditional logic, 27 field types, Stripe payments, and arbitrary Webhooks in the core plugin for free.

= Key Features =
* **Fast Drag & Drop Builder**: Built with modern React and TypeScript in the admin area.
* **Server-Rendered Public Forms**: Zero React runtime overhead on the public site for maximum page load speed and SEO compatibility.
* **27 Core Field Types**: Single text, paragraph, name, email, phone, address, URL, number, slider, dropdown, radio, checkboxes, date/time, file upload, password, hidden, page break, section, HTML, star rating, Likert scale, NPS, layout columns, repeater, single item payment, multiple items payment, total price.
* **Free Conditional Logic**: Show/hide fields, skip pages, and conditionally trigger email notifications and confirmations.
* **Anti-Spam Suite**: Built-in honeypot + time-trap token, plus reCAPTCHA v2/v3, hCaptcha, and Cloudflare Turnstile integrations.
* **Integrations**: Stripe payments (PaymentIntents + Webhooks), Mailchimp opt-in, and free Webhooks.
* **20 Pre-Built Templates**: One-click form creation for contact, quote, survey, booking, and more.
* **WPForms Importer**: Effortlessly import forms and field settings from WPForms Lite.

== Installation ==

1. Upload the `formvox` directory to `/wp-content/plugins/`.
2. Activate FormVox through the 'Plugins' menu in WordPress.
3. Navigate to **FormVox > Add New Form** to create your first form.

== Frequently Asked Questions ==

= Is conditional logic free? =
Yes! Conditional logic is included in FormVox Core at zero additional cost.

= Are entries stored as custom database tables? =
Yes. Entries are stored in custom database tables `{prefix}formvox_entries` for fast querying and scaling.

== Screenshots ==

1. Form Builder Canvas and Field Palette.
2. Form Entries List View.
3. Form Settings and Integrations.

== Changelog ==

= 1.0.0 =
* Initial release of FormVox Core Plugin.
