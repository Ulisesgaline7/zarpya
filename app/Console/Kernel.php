<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Verificar clima cada 15 minutos y activar/desactivar multiplicador de lluvia
        $schedule->job(\App\Jobs\CheckWeatherConditionJob::class)
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->name('check-weather');

        // Actualizar demanda activa en Redis cada 5 minutos (pedidos activos por zona)
        $schedule->call(function () {
            \App\Models\Zone::where('status', 1)->each(function ($zone) {
                $activeOrders = \App\Models\Order::where('zone_id', $zone->id)
                    ->whereIn('order_status', ['pending', 'accepted', 'processing', 'handover', 'picked_up'])
                    ->count();
                \App\CentralLogics\DeliveryPricingService::setZoneDemand($zone->id, $activeOrders);
            });
        })->everyFiveMinutes()->name('update-demand-cache');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
