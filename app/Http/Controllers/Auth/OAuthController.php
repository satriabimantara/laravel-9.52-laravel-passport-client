<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OAuthController extends Controller
{
    //client dari OAuth
    protected $client;
    private $url_server;
    private $url_third_party;
    private $client_id;
    private $client_secret;
    public function __construct(Client $client)
    {
        $this->middleware('auth');
        $this->client = $client;
        $this->client_id = env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID', NULL);
        $this->client_secret = env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET', NULL);
        $this->url_server = 'http://127.0.0.1:8000';
        $this->url_third_party = 'http://127.0.0.1:8080';
    }
    public function redirect(Request $request)
    {
        // akan melakukan request ke aplikasi server
        // http://127.0.0.1:8000/oauth/authorize?your_query_string
        $request->session()->put('state', $state = Str::random(40));
        $query = http_build_query([
            'client_id' => $this->client_id,
            'redirect_uri' => $this->url_third_party . '/auth/passport/callback',
            'response_type' => 'code',
            'scope' => 'view-tweet post-tweet',
            'state' => $state,
            // 'prompt' => '', // "none", "consent", or "login"
        ]);

        return redirect($this->url_server . '/oauth/authorize?' . $query);
    }
    public function callback(Request $request)
    {

        // jika tidak menggunakan guzzlehttp
        // $response = Http::asForm()->post($this->url_server . '/oauth/token', [
        //     'grant_type' => 'authorization_code',
        //     'client_id' => $this->client_id,
        //     'client_secret' => $this->client_secret,
        //     'redirect_uri' => $this->url_third_party . '/auth/passport/callback',
        //     'code' => $request->code,
        // ]);
        // Jika menggunakan guzzle
        $response = $this->client->post($this->url_server . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri' => $this->url_third_party . '/auth/passport/callback',
                'code' => $request->code,
            ]
        ]);
        /*
        * This /oauth/token route will return a JSON response containing access_token, refresh_token, and expires_in attributes. The expires_in attribute contains the number of seconds until the access token expires.
        */
        $response = json_decode($response->getBody());
        // simpan token ke dalam user agar access_tokennya bisa digunakan untuk akses API aplikasi server
        $request->user()->token()->delete();
        $request->user()->token()->create([
            'expires_in' => $response->expires_in,
            'access_token' => $response->access_token,
            'refresh_token' => $response->refresh_token,
        ]);

        return redirect('/home');
    }
    public function refresh(Request $request)
    {
        $response = $this->client->post($this->url_server . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->user()->token->refresh_token,
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope' => 'view-tweet post-tweet'
            ]
        ]);

        $response = json_decode($response->getBody());

        // perbarui data access_token
        $request->user()->token()->update([
            'expires_in' => $response->expires_in,
            'access_token' => $response->access_token,
            'refresh_token' => $response->refresh_token,
        ]);
        return redirect()->back();
    }
}
