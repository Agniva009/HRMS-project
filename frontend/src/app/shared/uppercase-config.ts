/**
 * Central configuration listing fields that must be EXCLUDED from
 * automatic uppercase transformation.
 *
 * Add any new exception field names here — both the Angular directive
 * and the Laravel middleware reference a configuration list like this
 * so logic stays DRY.
 */
export const UPPERCASE_EXCEPTION_FIELDS: ReadonlySet<string> = new Set([
  'email',
  'email_address',
  'emailAddress',
  'username',
  'user_name',
  'password',
  'password_confirmation',
  'passwordConfirmation',
  'url',
  'website',
  'link',
  'callback_url',
  'redirect_uri',
  'system_id',
  'token',
  'api_key',
]);
