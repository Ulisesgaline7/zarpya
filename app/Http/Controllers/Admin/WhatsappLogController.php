<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\WhatsappNotificationService;
use App\Http\Controllers\Controller;
use App\Models\WhatsappNotificationLog;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class WhatsappLogController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->status;
        $search   = $request->search;
        $template = $request->template;

        $logs = WhatsappNotificationLog::when($status,   fn ($q) => $q->where('status', $status))
            ->when($template, fn ($q) => $q->where('template_name', $template))
            ->when($search,   fn ($q) => $q->where('to_phone', 'like', "%{$search}%"))
            ->latest()
            ->paginate(config('default_pagination'));

        $stats = [
            'total'     => WhatsappNotificationLog::count(),
            'sent'      => WhatsappNotificationLog::where('status', 'sent')->count(),
            'delivered' => WhatsappNotificationLog::where('status', 'delivered')->count(),
            'failed'    => WhatsappNotificationLog::where('status', 'failed')->count(),
        ];

        $templates = WhatsappNotificationLog::select('template_name')
            ->distinct()->pluck('template_name');

        return view('admin-views.zarpya-whatsapp.index', compact('logs', 'stats', 'templates', 'status', 'search', 'template'));
    }

    /** Reenviar un mensaje fallido */
    public function retry($id)
    {
        $log = WhatsappNotificationLog::where('status', 'failed')->findOrFail($id);

        $newLog = WhatsappNotificationService::send(
            phone:          $log->to_phone,
            templateName:   $log->template_name,
            params:         $log->template_params ?? [],
            notifiableType: $log->notifiable_type,
            notifiableId:   $log->notifiable_id,
        );

        Toastr::success($newLog->status === 'sent' ? 'Mensaje reenviado.' : 'Error al reenviar.');
        return back();
    }

    /** Webhook Twilio para actualizar estado de entrega */
    public function webhook(Request $request)
    {
        $sid    = $request->input('SmsSid') ?? $request->input('MessageSid');
        $status = $request->input('MessageStatus');

        if ($sid && $status) {
            $map = [
                'delivered' => 'delivered',
                'read'      => 'read',
                'failed'    => 'failed',
                'undelivered' => 'failed',
            ];

            WhatsappNotificationLog::where('twilio_sid', $sid)->update([
                'status'       => $map[$status] ?? $status,
                'delivered_at' => in_array($status, ['delivered', 'read']) ? now() : null,
            ]);
        }

        return response()->noContent();
    }
}
