<?php
/**
 * SMTP mailer — configures PHPMailer from environment variables.
 * Compatible with Brevo (smtp-relay.brevo.com) and any SMTP provider.
 *
 * Required .env vars:
 *   MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD
 *   MAIL_ENCRYPTION (tls|ssl), MAIL_FROM_ADDRESS, MAIL_FROM_NAME
 *
 * Set MAIL_ENABLED=false to disable without removing the vars.
 */

add_action('phpmailer_init', function (PHPMailer\PHPMailer\PHPMailer $mailer): void {
    if (env('MAIL_ENABLED') === false || env('MAIL_ENABLED') === 'false') {
        return;
    }

    $mailer->isSMTP();
    $mailer->Host       = env('MAIL_HOST');
    $mailer->Port       = (int) env('MAIL_PORT');
    $mailer->SMTPAuth   = true;
    $mailer->Username   = env('MAIL_USERNAME');
    $mailer->Password   = env('MAIL_PASSWORD');
    $mailer->SMTPSecure = env('MAIL_ENCRYPTION') === 'ssl'
        ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

    if (env('MAIL_FROM_ADDRESS')) {
        $mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME') ?: '');
    }
});
