# FormVox Developer Hooks & Filters Documentation

FormVox is designed to be fully extensible by developers and Pro addons.

## Action Hooks

### `formvox_process_entry`
Fires immediately after an entry has been inserted into the database.
```php
add_action( 'formvox_process_entry', function( $entry_id, $form, $submitted_fields ) {
    // Custom post-submission processing logic
}, 10, 3 );
```

### `formvox_stripe_payment_process`
Fires when processing Stripe payments.
```php
add_action( 'formvox_stripe_payment_process', function( $form, $entry_id, $fields_data ) {
    // Custom Stripe charge processing
}, 10, 3 );
```

## Filter Hooks

### `formvox_field_types`
Filter registered field type classes. Use this filter in Pro/addons to register custom field types.
```php
add_filter( 'formvox_field_types', function( $fields ) {
    $fields['signature'] = new \FormVoxPro\Fields\SignatureField();
    return $fields;
} );
```

### `formvox_notification_email`
Filter notification email data before sending via `wp_mail`.
```php
add_filter( 'formvox_notification_email', function( $email_data, $form, $entry_id ) {
    $email_data['headers'][] = 'Bcc: audit@example.com';
    return $email_data;
}, 10, 3 );
```
