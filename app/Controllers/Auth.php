<?php

namespace App\Controllers;

class Auth extends BaseController
{
    private $baseUrl = 'https://take-home-test-api.nutech-integrasi.com/';

    public function index()
    {
        return view('pages/login');
    }

    public function doLogin()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . 'login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'email'         => $this->request->getPost('email'),
                'password'      => $this->request->getPost('password'),
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
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
            $session = session();
            $session->set([
                'access_token'  => $body['data']['token'],
                'expired_at'    => time() + (12 * 60 * 60),
                'isLoggedIn'    => true
            ]);

            return $this->response->setJSON([
                'status'    => true,
                'message'   => 'Login berhasil',
                'redirect'  => '/'
            ]);
        } else {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => $body['message']
            ]);
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }

    public function register()
    {
        return view('pages/register');
    }

    public function doRegister()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email'             => 'required|valid_email',
            'first_name'        => 'required|min_length[3]',
            'last_name'         => 'required|min_length[3]',
            'password'          => 'required|min_length[6]',
            'retype_password'   => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $validation->getErrors()
            ]);
        }


        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . 'registration',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'email'         => $this->request->getPost('email'),
                'first_name'    => $this->request->getPost('first_name'),
                'last_name'     => $this->request->getPost('last_name'),
                'password'      => $this->request->getPost('password'),
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
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
                'redirect' => '/login'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => $body['message']
            ]);
        }
    }
}
