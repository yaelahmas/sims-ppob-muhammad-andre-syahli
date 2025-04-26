<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Transaction extends Controller
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

        return view('pages/transaction', [
            'profile' => $profile,
            'balance' => $balance
        ]);
    }

    public function doPayment($service_code)
    {
        $token = session()->get('access_token');
        $profile = $this->fetchData('profile', $token);
        $balance = $this->fetchData('balance', $token);
        $service = $this->fetchData('services', $token);

        // dd($profile, $balance, $service);

        if (isset($profile['error']) || isset($balance['error']) || isset($service['error'])) {
            return view('error_view', [
                'message' => 'Gagal mengambil salah satu data dari API.',
                'details' => [
                    'profile' => $profile['error'] ?? null,
                    'balance' => $balance['error'] ?? null,
                    'service' => $service['error'] ?? null,
                ]
            ]);
        }

        $services = array();
        foreach ($service['data'] as $row) {
            if (strtolower($row['service_code']) == $service_code) {
                $services[] = $row;
            }
        }

        return view('pages/transaction-payment', [
            'profile' => $profile,
            'balance' => $balance,
            'service' => $services[0],
        ]);
    }

    public function doTransaction()
    {

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . 'transaction',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'service_code' => $this->request->getPost('service_code')
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
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => $body['message']
            ]);
        }
    }

    public function doHistory()
    {
        $token          = session()->get('access_token');
        $offset         = (int) $this->request->getGet('offset') ?? 0;
        $limit          = (int) $this->request->getGet('limit') ?? 5;
        $endpoint       = "transaction/history?offset={$offset}&limit={$limit}";
        $transactions   = $this->fetchData($endpoint, $token);

        if (isset($transactions['error'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $transactions['error']
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'data'      => $transactions['data']['records'] ?? []
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
