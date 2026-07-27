export interface FieldRule {
  field_id: string;
  operator: 'equals' | 'not_equals' | 'contains' | 'empty' | 'not_empty';
  value: string;
}

export interface ConditionalLogic {
  enabled: boolean;
  action: 'show' | 'hide';
  match: 'all' | 'any';
  rules: FieldRule[];
}

export interface FieldOption {
  label: string;
  value: string;
}

export interface FieldConfig {
  id: string;
  type: string;
  label: string;
  description?: string;
  placeholder?: string;
  required?: boolean;
  css_class?: string;
  default_val?: string;
  options?: FieldOption[];
  conditional_logic?: ConditionalLogic;
  columns?: number;
  content?: string;
  min?: number;
  max?: number;
  step?: number;
}

export interface NotificationConfig {
  id: string;
  name: string;
  to_email: string;
  subject: string;
  message: string;
  reply_to?: string;
}

export interface ConfirmationConfig {
  id: string;
  type: 'message' | 'page' | 'redirect';
  message?: string;
  page_id?: number;
  redirect_url?: string;
}

export interface FormSchema {
  fields: FieldConfig[];
  settings: {
    title: string;
    description: string;
    submit_text: string;
    ajax_submit: boolean;
  };
  notifications: NotificationConfig[];
  confirmations: ConfirmationConfig[];
}

export interface FormRecord {
  id: number;
  title: string;
  status: string;
  schema: FormSchema;
  created_at: string;
  updated_at: string;
}

export interface EntryRecord {
  id: number;
  form_id: number;
  status: string;
  starred: number;
  is_read: number;
  ip_address: string;
  created_at: string;
  fields: Record<string, any>;
}
