<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Profile extends Controller
{
    private $baseUrl = 'https://take-home-test-api.nutech-integrasi.com/';

    public function index()
    {
        $profile = $this->getProfileOrRedirect();

        return view('pages/profile', [
            'profile' => $profile
        ]);
    }

    public function editProfile()
    {
        $profile = $this->getProfileOrRedirect();

        return view('pages/profile-edit', [
            'profile' => $profile
        ]);
    }

    public function doProfileUpdate()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'first_name'    => 'required|min_length[3]',
            'last_name'     => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . 'profile/update',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode([
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name')
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
                'status'    => false,
                'message'   => 'Gagal koneksi ke server: ' . $err
            ]);
        }

        $body = json_decode($response, true);

        if (isset($body['status']) && $body['status'] === 0) {
            return $this->response->setJSON([
                'status'    => true,
                'message'   => 'Update Pofile berhasil',
                'redirect'  => '/profile'
            ]);
        } else {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => $body['message']
            ]);
        }
    }

    public function doProfileImage()
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'File tidak valid.'
            ]);
        }

        $curl = curl_init();
        $cfile = new \CURLFile($file->getTempName(), $file->getMimeType(), $file->getName());

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . 'profile/image',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'Authorization: Bearer ' . session()->get('access_token'),
            ],
            CURLOPT_POSTFIELDS => [
                'file' => $cfile
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
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
                'redirect'  => '/profile'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => $body['message']
            ]);
        }
    }


    private function getProfileOrRedirect()
    {
        $token = session()->get('access_token');
        $profile = $this->fetchData('profile', $token);

        if (isset($profile['error'])) {
            echo view('error_view', [
                'message' => 'Gagal mengambil salah satu data dari API.',
                'details' => [
                    'profile' => $profile['error'] ?? null,
                ]
            ]);
            exit;
        }

        return $profile;
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
