<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $client;
    protected $url_server_api;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->middleware(['auth', 'refresh.token']);
        $this->client = $client;
        $this->url_server_api = "http://localhost:8000/api";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // dapatkan request api/tweets dari aplikasi server dengan access_token dari user yang sudah ada
        $tweets = collect();
        // lakukan validasi
        if ($request->user()->token) {
            // if ($request->user()->token->hasExpired()) {
            //     dd('Access Token was expired!');
            // }
            $response = $this->client->get($this->url_server_api . "/tweets", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $request->user()->token->access_token
                ]
            ]);
            $tweets = collect(json_decode($response->getBody()));
        }
        return view('home', [
            'tweets' => $tweets
        ]);
    }
}
