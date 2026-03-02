<?php

namespace App\Providers;

use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'auth.admin-dashboard',
            'auth.admin-accountant',
            'auth.admin-announcement',
            'auth.admin-consumer',
            'auth.admin-consumer-form',
            'auth.admin-plumber',
        ], function ($view) {
            if (!Auth::guard('admin')->check()) {
                $view->with('complaintConversations', collect());
                $view->with('totalComplaints', 0);
                return;
            }

            $complaints = Complaint::with(['consumer:id,first_name,middle_name,last_name,suffix,meter_no'])
                ->orderBy('created_at')
                ->get();

            $complaintConversations = $complaints
                ->groupBy('consumer_id')
                ->map(function ($messages) {
                    $consumer = optional($messages->first())->consumer;
                    $consumerName = trim(implode(' ', array_filter([
                        $consumer?->first_name,
                        $consumer?->middle_name,
                        $consumer?->last_name,
                        $consumer?->suffix,
                    ])));
                    $lastMessage = $messages->last();

                    return [
                        'consumer_id' => $messages->first()->consumer_id,
                        'consumer_name' => $consumerName ?: 'Unknown Consumer',
                        'meter_no' => $consumer?->meter_no ?? 'N/A',
                        'messages' => $messages,
                        'last_message' => $lastMessage?->plainMessage() ?? '',
                        'last_message_at' => $lastMessage?->created_at,
                    ];
                })
                ->sortByDesc(function (array $conversation) {
                    return $conversation['last_message_at'] ?? now()->subYears(50);
                })
                ->take(20)
                ->values();

            $view->with('complaintConversations', $complaintConversations);
            $view->with('totalComplaints', $complaints->count());
        });
    }
}
