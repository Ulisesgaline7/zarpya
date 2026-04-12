<?php

namespace App\CentralLogics;

use App\Models\WhatsappNotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsappNotificationService
 *
 * Envia notificaciones via Twilio WhatsApp API.
 * Numero base: +504 (Honduras).
 *
 * Configuracion en .env:
 *   TWILIO_ACCOUNT_SID=ACxxxx
 *   TWILIO_AUTH_TOKEN=xxxx
 *   TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
 */
class WhatsappNotificationService
{
    // ---------------------------------------------------------------
    // Templates predefinidos
    // ---------------------------------------------------------------

    const TEMPLATE_ORDER_CONFIRMED    = 'order_confirmed';
    const TEMPLATE_ORDER_ASSIGNED     = 'order_assigned';
    const TEMPLATE_ORDER_PICKED_UP    = 'order_picked_up';
    const TEMPLATE_ORDER_DELIVERED    = 'order_delivered';
    const TEMPLATE_ORDER_CANCELLED    = 'order_cancelled';
    const TEMPLATE_TAXI_ACCEPTED      = 'taxi_accepted';
    const TEMPLATE_TAXI_ARRIVING      = 'taxi_arriving';
    const TEMPLATE_TAXI_COMPLETED     = 'taxi_completed';
    const TEMPLATE_RENTAL_CONFIRMED   = 'rental_confirmed';
    const TEMPLATE_SERVICE_QUOTED     = 'service_quoted';
    const TEMPLATE_SERVICE_COMPLETED  = 'service_completed';
    const TEMPLATE_OTP                = 'otp_verification';

    /**
     * Mensajes de cada template (con placeholders {{1}}, {{2}}, ...).
     * En produccion reemplazar por templates aprobados de WhatsApp Business.
     */
    private static array $templates = [
        self::TEMPLATE_ORDER_CONFIRMED  => '✅ Tu pedido *#{{1}}* ha sido confirmado. Tiempo estimado: *{{2}} min*. ¡Gracias por usar Zarpya!',
        self::TEMPLATE_ORDER_ASSIGNED   => '🛵 Tu repartidor *{{1}}* ya tomó tu pedido *#{{2}}*. Puedes seguirlo en tiempo real.',
        self::TEMPLATE_ORDER_PICKED_UP  => '📦 Tu pedido *#{{1}}* ya fue recogido y está en camino. Llegará aprox. en *{{2}} min*.',
        self::TEMPLATE_ORDER_DELIVERED  => '🎉 ¡Tu pedido *#{{1}}* fue entregado! Califica tu experiencia en la app.',
        self::TEMPLATE_ORDER_CANCELLED  => '❌ Tu pedido *#{{1}}* fue cancelado. Motivo: {{2}}. Si tienes dudas escríbenos.',
        self::TEMPLATE_TAXI_ACCEPTED    => '🚗 Tu conductor *{{1}}* aceptó tu viaje. Llegará en aprox. *{{2}} min*. Placa: *{{3}}*.',
        self::TEMPLATE_TAXI_ARRIVING    => '📍 Tu conductor ya está llegando. ¡Prepárate!',
        self::TEMPLATE_TAXI_COMPLETED   => '✅ Viaje completado. Total cobrado: *L {{1}}*. ¡Gracias por viajar con Zarpya!',
        self::TEMPLATE_RENTAL_CONFIRMED => '🔑 Tu reserva de *{{1}}* confirmada del *{{2}}* al *{{3}}*. Total: *L {{4}}*.',
        self::TEMPLATE_SERVICE_QUOTED   => '🔧 *{{1}}* te envió una cotización de *L {{2}}* para tu solicitud. Acepta en la app.',
        self::TEMPLATE_SERVICE_COMPLETED=> '✅ Servicio completado. Total: *L {{1}}*. ¡Califica a {{2}} en Zarpya!',
        self::TEMPLATE_OTP              => '🔐 Tu código de verificación Zarpya es: *{{1}}*. Expira en 5 minutos. No lo compartas.',
    ];

    // ---------------------------------------------------------------
    // Envio principal
    // ---------------------------------------------------------------

    /**
     * Envia un mensaje de WhatsApp y registra en BD.
     *
     * @param  string      $phone           Numero destino (ej. '99999999' o '+50499999999')
     * @param  string      $templateName    Constante TEMPLATE_*
     * @param  array       $params          Variables del template ['John', '30', ...]
     * @param  string|null $notifiableType  Modelo relacionado (Order, TaxiRide, etc.)
     * @param  int|null    $notifiableId
     */
    public static function send(
        string $phone,
        string $templateName,
        array $params = [],
        ?string $notifiableType = null,
        ?int $notifiableId = null
    ): WhatsappNotificationLog {
        $phone = self::normalizePhone($phone);
        $body  = self::renderTemplate($templateName, $params);

        $log = WhatsappNotificationLog::create([
            'to_phone'        => $phone,
            'template_name'   => $templateName,
            'template_params' => $params,
            'message_body'    => $body,
            'status'          => 'pending',
            'notifiable_type' => $notifiableType,
            'notifiable_id'   => $notifiableId,
        ]);

        try {
            $sid = self::sendViaTwilio($phone, $body);

            $log->update([
                'twilio_sid' => $sid,
                'status'     => 'sent',
                'sent_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', [
                'phone'    => $phone,
                'template' => $templateName,
                'error'    => $e->getMessage(),
            ]);

            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    // ---------------------------------------------------------------
    // Atajos por evento
    // ---------------------------------------------------------------

    public static function orderConfirmed(string $phone, string $orderId, int $etaMin, int $notifiableId): void
    {
        self::send($phone, self::TEMPLATE_ORDER_CONFIRMED, [$orderId, (string) $etaMin], 'App\\Models\\Order', $notifiableId);
    }

    public static function orderAssigned(string $phone, string $driverName, string $orderId, int $notifiableId): void
    {
        self::send($phone, self::TEMPLATE_ORDER_ASSIGNED, [$driverName, $orderId], 'App\\Models\\Order', $notifiableId);
    }

    public static function orderPickedUp(string $phone, string $orderId, int $etaMin, int $notifiableId): void
    {
        self::send($phone, self::TEMPLATE_ORDER_PICKED_UP, [$orderId, (string) $etaMin], 'App\\Models\\Order', $notifiableId);
    }

    public static function orderDelivered(string $phone, string $orderId, int $notifiableId): void
    {
        self::send($phone, self::TEMPLATE_ORDER_DELIVERED, [$orderId], 'App\\Models\\Order', $notifiableId);
    }

    public static function orderCancelled(string $phone, string $orderId, string $reason, int $notifiableId): void
    {
        self::send($phone, self::TEMPLATE_ORDER_CANCELLED, [$orderId, $reason], 'App\\Models\\Order', $notifiableId);
    }

    public static function taxiAccepted(string $phone, string $driverName, int $etaMin, string $plate, int $rideId): void
    {
        self::send($phone, self::TEMPLATE_TAXI_ACCEPTED, [$driverName, (string) $etaMin, $plate], 'App\\Models\\TaxiRide', $rideId);
    }

    public static function taxiCompleted(string $phone, float $totalFare, int $rideId): void
    {
        self::send($phone, self::TEMPLATE_TAXI_COMPLETED, [number_format($totalFare, 2)], 'App\\Models\\TaxiRide', $rideId);
    }

    public static function rentalConfirmed(string $phone, string $vehicleName, string $startDate, string $endDate, float $total, int $bookingId): void
    {
        self::send($phone, self::TEMPLATE_RENTAL_CONFIRMED, [
            $vehicleName, $startDate, $endDate, number_format($total, 2),
        ], 'App\\Models\\RentalBooking', $bookingId);
    }

    public static function serviceQuoted(string $phone, string $providerName, float $amount, int $requestId): void
    {
        self::send($phone, self::TEMPLATE_SERVICE_QUOTED, [
            $providerName, number_format($amount, 2),
        ], 'App\\Models\\ServiceRequest', $requestId);
    }

    public static function sendOtp(string $phone, string $code): void
    {
        self::send($phone, self::TEMPLATE_OTP, [$code]);
    }

    // ---------------------------------------------------------------
    // Helpers internos
    // ---------------------------------------------------------------

    /**
     * Normaliza numero al formato +504XXXXXXXX (Honduras).
     */
    public static function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Si ya tiene codigo de pais
        if (strlen($clean) > 8) {
            return '+' . $clean;
        }

        return '+504' . $clean;
    }

    private static function renderTemplate(string $templateName, array $params): string
    {
        $tpl = self::$templates[$templateName] ?? $templateName;

        foreach ($params as $i => $value) {
            $tpl = str_replace('{{' . ($i + 1) . '}}', $value, $tpl);
        }

        return $tpl;
    }

    /**
     * Llama a Twilio Messages API para WhatsApp.
     * Retorna el SID del mensaje.
     */
    private static function sendViaTwilio(string $toPhone, string $body): string
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.whatsapp_from', 'whatsapp:+14155238886');

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To'   => 'whatsapp:' . $toPhone,
                'Body' => $body,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Twilio error: ' . ($response->json('message') ?? $response->body())
            );
        }

        return $response->json('sid');
    }
}
