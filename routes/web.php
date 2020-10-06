<?php
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return '小小新闻';
    // return $router->app->version();
});

$router->get('/privacy-policy', function() {
    return View::make('privacy-policy');
});

$router->get('/everything', function(Request $request) {

    $query = $request->only([
        'q',
        'language',
        'page',
        'pageSize'
    ]);
    $key = '/everything?'.http_build_query($query);
    $contents = Cache::get($key);
    if (is_null($contents)) {
        $client = new Client([
            'base_uri' => 'https://newsapi.org/',
            'proxy' => env('NEWSAPI_PROXY')
        ]);
        $response = $client->request('GET', 'v2/everything', [
            'query' => $query,
            'headers' => [
                'x-api-key' => env('NEWSAPI_KEY')
            ]
        ]);
        $contents = $response->getBody()->getContents();
        Cache::put($key, $contents, 1800);
    }
    return response($contents)->header('content-type', 'application/json');
});
