<?php

namespace Modules\Whatsi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class Main extends Controller
{
    public function setAPI()
    {
        $vendor = auth()->user()->restaurant;
        if($vendor){
            return view('whatsi::set_api',[
                'api_key' => $vendor->getConfig('whatsi_api',''),
                'base_url' => $vendor->getConfig('whatsi_base_url', config('whatsi.evo_base_url'))
            ]);
        }
    }

    public function set_campaings()
    {
        $vendor = auth()->user()->restaurant;
        if($vendor && $vendor->getConfig('whatsi_api','') == ""){
            return $this->setAPI();
        }

        $api_key = $vendor->getConfig('whatsi_api','');
        $base_url = $vendor->getConfig('whatsi_base_url', config('whatsi.evo_base_url'));

        if(request()->input('selected_id')){
            $vendor->setConfig('whatsi_status_change_campaigns',request()->input('selected_id'));
        }

        try {
            $campaigns = $this->getAPICampaigns($api_key, $base_url);

            if(isset($campaigns['status']) && $campaigns['status'] == 'success'){
                $campaignItems = $campaigns['items'];
                
                if(count($campaignItems) > 0){
                    $campaignsData = [];
                    foreach($campaignItems as $campaign){
                        $campaignsData[$campaign['id']] = $campaign['name'];
                    }
               
                    $selected_campaign = $vendor->getConfig('whatsi_status_change_campaigns',"");
                    return view('whatsi::campaings',['campaigns' => $campaignsData, 'selected_campaign' => $selected_campaign]);
                } else {
                    return view('whatsi::set_api',['error' => 'No campaigns found', 'api_key' => $api_key, 'base_url' => $base_url]);
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('whatsi.set_api',['error' => 'Error in getting the campaigns', 'api_key' => $api_key, 'base_url' => $base_url]);
        }

        return view('whatsi::set_api',['error' => 'Error in getting the campaigns. Make sure your API Key is correct.', 'api_key' => $api_key, 'base_url' => $base_url]);
    }

    public function getAPICampaigns($api_key, $base_url)
    {
        $url = rtrim($base_url, '/') . '/api/wpbox/getCampaigns?token=' . $api_key . "&type=api";
        $response = Http::get($url);
        return $response->json();
    }

    public function store_api_key(Request $request)
    {
        $api_key = $request->input('api_key');
        $base_url = $request->input('base_url');
        $vendor = auth()->user()->restaurant;
        $vendor->setConfig('whatsi_api', $api_key);
        $vendor->setConfig('whatsi_base_url', $base_url);
        return redirect()->route('whatsi.campaings');
    }

    public function connect(Request $request)
    {
        $vendor = auth()->user()->restaurant;
        $api_key = $vendor->getConfig('whatsi_api','');
        $base_url = $vendor->getConfig('whatsi_base_url', config('whatsi.evo_base_url'));
        $instance_name = 'CitasPro_' . $vendor->id;

        try {
            $response = Http::withHeaders(['apikey' => $api_key])
                ->get($base_url . '/instance/connect/' . $instance_name);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to connect'], 500);
        }
    }

    public function status(Request $request)
    {
        $vendor = auth()->user()->restaurant;
        $api_key = $vendor->getConfig('whatsi_api','');
        $base_url = $vendor->getConfig('whatsi_base_url', config('whatsi.evo_base_url'));
        $instance_name = 'CitasPro_' . $vendor->id;

        try {
            $response = Http::withHeaders(['apikey' => $api_key])
                ->get($base_url . '/instance/connectionState/' . $instance_name);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get status'], 500);
        }
    }

    public function delete(Request $request)
    {
        $vendor = auth()->user()->restaurant;
        $api_key = $vendor->getConfig('whatsi_api','');
        $base_url = $vendor->getConfig('whatsi_base_url', config('whatsi.evo_base_url'));
        $instance_name = 'CitasPro_' . $vendor->id;

        try {
            $response = Http::withHeaders(['apikey' => $api_key])
                ->delete($base_url . '/instance/logout/' . $instance_name);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete instance'], 500);
        }
    }
}
