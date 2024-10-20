<?php

namespace Modules\Whatsi\Listeners;

use App\Order;
use GuzzleHttp\Client;

class WebhookOrder
{
    public function handleUpdateOrder($event)
    {
        $order = Order::where('id', $event->order["id"])->first();
        if ($order) {
            $vendor = $order->restorant;

            // Vendor setup
            $vendorApiToken = $vendor->getConfig('whatsi_api', '');
            $webhook_for_notify = config('whatsi.evo_base_url', 'https://evolution-api.com');

            // GET THE PHONE NUMBER
            $phone = null;
            if ($order->client) {
                $phone = $order->client->phone;
            }

            // If no phone, then get the phone from the order phone
            if (!$phone) {
                $phone = $order->phone;
            }

            // If no phone, then get the phone from the custom field phone
            if (!$phone) {
                $phone = $order->getConfig('client_phone', null);
            }

            // If no phone, or empty, don't send the message
            if (!$phone || $phone == "" || strlen($phone) < 5) {
                return;
            }

            $campaing_id = $vendor->getConfig('whatsi_status_change_campaigns', '');
            if ($campaing_id == "") {
                return;
            }

            $dataToSend = [
                'phone' => $phone,
                'token' => $vendorApiToken,
                'campaing_id' => $campaing_id,
                'data' => $order->toArray(),
            ];
            $dataToSend['data']['custom_fields'] = $order->getAllConfigs();
            $dataToSend['data']['items'] = $order->items->toArray();
            $dataToSend['data']['last_status'] = ["name" => $order->laststatus()->get()[0]->name];

            $client = new Client();
            $payload = [
                'form_params' => $dataToSend,
            ];

            $response = $client->request('POST', $webhook_for_notify . "/api/wpbox/sendcampaigns", $payload);
        }
    }

    public function subscribe($events)
    {
        try {
            $events->listen(
                'App\Events\UpdateOrder',
                [WebhookOrder::class, 'handleUpdateOrder']
            );
        } catch (\Exception $e) {
            // If debug is enabled, show the error
            if (config('app.debug')) {
                dd($e);
            }
        }
    }
}
