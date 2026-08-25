<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function newsletter_error_log(string $message, array $context = []): void
{
    $logFile = __DIR__ . '/../controller/newsletter_error.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
    $logEntry = "[{$timestamp}] {$message}{$contextStr}" . PHP_EOL;

    if (!@file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX)) {
        error_log("[NEWSLETTER_ERROR] {$message}{$contextStr}");
    }
}

function notify_newsletter_subscribers($conn, string $tipo, array $item): array
{
    $tipo = ($tipo === 'galeria') ? 'galeria' : 'blog';

    $emails = [];
    $res = mysqli_query($conn, "SELECT email FROM customers WHERE newsletter_subscribed = 1 AND is_active = 1");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $email = trim($row['email']);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }
    }
    $emails = array_values(array_unique($emails));

    if (empty($emails)) {
        return ['success' => true, 'sent' => 0, 'error' => ''];
    }

    try {
        $smtp = null;
        $resSmtp = mysqli_query($conn, "SELECT * FROM smtpconfig WHERE id = 1 LIMIT 1");
        if ($resSmtp && mysqli_num_rows($resSmtp) > 0) {
            $smtp = mysqli_fetch_assoc($resSmtp);
        }
        if (!$smtp) {
            throw new Exception('No se encontró configuración SMTP.');
        }

        $company = ['nombre' => 'Poder Down'];
        $resCompany = mysqli_query($conn, "SELECT nombre FROM company WHERE id = 1 LIMIT 1");
        if ($resCompany && mysqli_num_rows($resCompany) > 0) {
            $company = mysqli_fetch_assoc($resCompany);
        }

        $title    = trim($item['title'] ?? '');
        $slug     = trim($item['slug'] ?? '');
        $excerpt  = trim($item['excerpt'] ?? '');
        $featured = trim($item['featured_image'] ?? '');

        $esGaleria = ($tipo === 'galeria');
        $empresa   = htmlspecialchars($company['nombre']);
        $tituloH   = htmlspecialchars($title);
        $extractoH = htmlspecialchars($excerpt);
        $label     = $esGaleria ? 'Nueva galería publicada' : 'Nuevo blog publicado';
        $cta       = $esGaleria ? 'Ver galería' : 'Ver blog';
        $tipoTexto = $esGaleria ? 'galería' : 'blog';

        $base = 'https://poderdown.com/';
        $url  = $base . $tipo . '?slug=' . rawurlencode($slug);
        $urlH = htmlspecialchars($url);

        $excerptHtml = '';
        if ($excerpt !== '') {
            $excerptHtml = "<p style='font-size:14px;color:#555;line-height:1.7;margin:0 0 20px;'>{$extractoH}</p>";
        }

        $imgHtml = '';
        if ($featured !== '') {
            $imgUrl = htmlspecialchars($featured);
            $imgHtml = "<div style='text-align:center;margin:0 0 20px;'><img src='{$imgUrl}' alt='' style='max-width:100%;border-radius:12px;'></div>";
        }

        $asunto = $label . ': ' . $title . ' — ' . $empresa;

        $body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#ebeae4;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#ebeae4;padding:30px 0;">
    <tr><td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(26,58,92,.08);">
            <tr>
                <td style="background:linear-gradient(135deg,#1A3A5C,#0D2136);padding:28px 30px;text-align:center;">
                    <h1 style="font-family:'Trebuchet MS',Arial,sans-serif;font-size:22px;color:#fff;margin:0;">{$label}</h1>
                </td>
            </tr>
            <tr>
                <td style="padding:28px 30px;">
                    <h2 style="font-size:18px;color:#1A3A5C;margin:0 0 16px;">{$tituloH}</h2>
                    {$excerptHtml}
                    {$imgHtml}
                    <div style="text-align:center;margin:24px 0 8px;">
                        <a href="{$urlH}" style="display:inline-block;background:#3CAEE0;color:#fff;text-decoration:none;padding:12px 28px;border-radius:24px;font-size:15px;font-weight:700;">{$cta}</a>
                    </div>
                    <p style="font-size:13px;color:#888;line-height:1.7;margin:20px 0 0;">
                        Si tienes alguna duda, escríbenos a <a href="mailto:info@poderdown.com" style="color:#3CAEE0;">info@poderdown.com</a>.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="background:#f0efe9;padding:18px 30px;text-align:center;">
                    <p style="font-size:12px;color:#999;margin:0;">{$empresa} &mdash; Arte e inclusión<br><a href="https://poderdown.com" style="color:#3CAEE0;text-decoration:none;">poderdown.com</a></p>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
HTML;

        $altBody = "¡Hola!\n\nSe ha publicado un nuevo {$tipoTexto}: \"{$title}\".\n\n"
            . ($excerpt !== '' ? $excerpt . "\n\n" : '')
            . "Puedes verlo aquí: {$url}\n\n"
            . "{$company['nombre']} — poderdown.com";

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = !empty($smtp['username']) ? $smtp['username'] : $smtp['email'];
        $mail->Password = $smtp['password'];
        $mail->Port = (int)$smtp['port'];
        $mail->SMTPSecure = ((int)$smtp['port'] === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Timeout = 60;
        $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];

        $fromEmail = !empty($smtp['email']) ? $smtp['email'] : 'noreply@poderdown.com';
        $mail->setFrom($fromEmail, $empresa);
        foreach ($emails as $addr) {
            $mail->addBCC($addr);
        }
        $mail->isHTML(true);
        $mail->Subject = $asunto;

        $mail->Body = $body;
        $mail->AltBody = $altBody;
        $mail->send();

        return ['success' => true, 'sent' => count($emails), 'error' => ''];
    } catch (Throwable $e) {
        newsletter_error_log('newsletter ' . $tipo . ' ERROR: ' . $e->getMessage(), [
            'tipo'       => $tipo,
            'title'      => $item['title'] ?? '',
            'slug'       => $item['slug'] ?? '',
            'recipients' => count($emails),
        ]);
        return ['success' => false, 'sent' => 0, 'error' => $e->getMessage()];
    }
}
