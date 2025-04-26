<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends Controller
{
    private $baseUrl = 'https://take-home-test-api.nutech-integrasi.com/';

    public function index()
    {
        $token      = session()->get('access_token');
        $profile    = $this->fetchData('profile', $token);
        $balance    = $this->fetchData('balance', $token);
        $services   = $this->fetchData('services', $token);
        $banners    = $this->fetchData('banner', $token);

        // dd($profile, $balance, $services, $banners);

        if (isset($profile['error']) || isset($balance['error']) || isset($services['error']) || isset($banners['error'])) {
            return view('error_view', [
                'message' => 'Gagal mengambil salah satu data dari API.',
                'details' => [
                    'profile'   => $profile['error'] ?? null,
                    'balance'   => $balance['error'] ?? null,
                    'services'  => $services['error'] ?? null,
                    'banners'   => $banners['error'] ?? null,
                ]
            ]);
        }

        return view('pages/dashboard', [
            'profile'   => $profile,
            'balance'   => $balance,
            'services'  => $services,
            'banners'   => $banners
        ]);
    }

    private function fetchData($endpoint, $token)
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $token
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status_code == 200) {
            return json_decode($response, true);
        } else {
            return ['error' => 'Status code: ' . $status_code . '. Response: ' . $response];
        }
    }
}
