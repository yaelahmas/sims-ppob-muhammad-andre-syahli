<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Topup extends Controller
{
    private $baseUrl = 'https://take-home-test-api.nutech-integrasi.com/';

    public function index()
    {
        $token = session()->get('access_token');
        $profile = $this->fetchData('profile', $token);
        $balance = $this->fetchData('balance', $token);

        // dd($profile, $balance, $transactions);

        if (isset($profile['error']) || isset($balance['error'])) {
            return view('error_view', [
                'message' => 'Gagal mengambil salah satu data dari API.',
                'details' => [
                    'profile' => $profile['error'] ?? null,
                    'balance' => $balance['error'] ?? null,
                ]
            ]);
        }

        return view('pages/topup', [
            'profile' => $profile,
            'balance' => $balance
        ]);
    }

    public function doTopup()
    {
        $top_up_amount = $this->request->getPost('top_up_amount');

        if (!$top_up_amount || !is_numeric($top_up_amount) || $top_up_amount <= 0) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Jumlah top up tidak valid.'
            ]);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . 'topup',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'top_up_amount' => (int) $top_up_amount
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: ' . 'Bearer ' . session()->get('access_token')
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal koneksi ke server: ' . $err
            ]);
        }

        $body = json_decode($response, true);

        if (isset($body['status']) && $body['status'] === 0) {
            return $this->response->setJSON([
                'status' => true,
                'message' => $body['message'],
                'redirect'  => '/topup'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => $body['message']
            ]);
        }
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
