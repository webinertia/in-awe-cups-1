<?php

declare(strict_types=1);

return [
    'log_generic_404'                 => 'The requested resource could not be found.',
    'log_500'                         => 'The server encountered an error. Please try again later.',
    'log_contact_email_success'       => 'Guest: %s has sent a message using email: %s',
    'log_contact_email_failure'       => 'Failed to send contact form email. From Name: %s, From Email: %s',
    'log_failed_password_reset_ip'    => 'Unknown user using IP: %s attempted to reset password with invalid or expired token',
    'log_settings_file_write_success' => 'Settings updated successfully.',
    'log_settings_file_write_failure' => 'System failed to write to settings file. Please check file permissions.',
    'log_settings_file_read_failure'  => 'System failed to read from settings file. Please check file permissions and path.',
];
