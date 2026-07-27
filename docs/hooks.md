# FormsVox Developer Hooks & Filters Documentation

FormsVox is designed to be fully extensible by developers and Pro addons.

## Action Hooks

### `formsvox_process_entry`
Fires immediately after an entry has been inserted into the database.
```php
add_action( 'formsvox_process_entry', function( $entry_id, $form, $submitted_fields ) {
    // Custom post-submission processing logic
}, 10, 3 );
```

### `formsvox_stripe_payment_process`
Fires when processing Stripe payments.
```php
add_action( 'formsvox_stripe_payment_process', function( $form, $entry_id, $fields_data ) {
    // Custom Stripe charge processing
}, 10, 3 );
```

## Filter Hooks

### `formsvox_field_types`
Filter registered field type classes. Use this filter in Pro/addons to register custom field types.
```php
add_filter( 'formsvox_field_types', function( $fields ) {
    $fields['signature'] = new \FormsVoxPro\Fields\SignatureField();
    return $fields;
} );
```

### `formsvox_notification_email`
Filter notification email data before sending via `wp_mail`.
```php
add_filter( 'formsvox_notification_email', function( $email_data, $form, $entry_id ) {
    $email_data['headers'][] = 'Bcc: audit@example.com';
    return $email_data;
}, 10, 3 );
```
