<?php

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;


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

$app->get('/', function () use ($app) {
    return view('index');
});

$app->get('/api/v1/songs/latest', function(Repository $cache) use ($app) {
  $cacheKey = 'latest-songs';

  if($cache->has('latest-songs')) {
    $latestSongs = $cache->get($cacheKey);
  } else {
    $response = file_get_contents('http://www.singsingmedia.com/songsearch/');
    preg_match_all(
      "/id=\"songlist\".*\n.*h6>(.*)<\/h6/",
      $response,
      $titleMatch
    );
    preg_match_all(
      "/number=\"(\d+)\".*\n.*title\">(.*)<.*\n.*artist\">(.*).*\n.*lang\">(\w+)/",
      $response,
      $songMatches
    );
    $idx = -1;
    $latestSongs = [
        'title' => count($titleMatch) > 1 ? strip_tags($titleMatch[1][0]) : 'Not Found',
        'songs' => array_map(function($match) use ($songMatches, &$idx) {
          $idx++;
          return [
              'id' => strip_tags($songMatches[1][$idx]),
              'name' => strip_tags($songMatches[2][$idx]),
              'artist' => strip_tags($songMatches[3][$idx]),
              'language' => strip_tags($songMatches[4][$idx])
          ];
        }, count($songMatches) > 4 ? $songMatches[1] : [])
    ];
    $expiresAt = Carbon::now()->addMinutes(10);

    $cache->put($cacheKey, $latestSongs, $expiresAt);
  }
  return response()->json($latestSongs);
});

$app->get('/api/v1/songs/search/{query}', function($query, Repository $cache) use ($app) {
  $query = urldecode($query);
  // die($query);
  $cacheKey = 'search:' . $query;
  if($cache->has($cacheKey)) {
    $searchResults = $cache->get($cacheKey);
  } else {
    $postData = http_build_query(['query' => $query]);
    $opts = ['http' => [
      'method' => 'POST',
      'header' => 'Content-type: application/x-www-form-urlencoded',
      'content' => $postData
    ]];
    $context  = stream_context_create($opts);

    $response = file_get_contents(
      'http://www.singsingmedia.com/website/parts/songsearch.ajax.php',
      false,
      $context
    );
    // var_dump($response); die();
    preg_match_all(
      "/number>(\d+).*\n.*title>(.*)<.*\n.*artist>(.*)<.*\n.*lang>(\w+)</",
      $response,
      $songMatches
    );
    $idx = -1;
    // var_dump($songMatches); die();
    $searchResults = [
        'songs' => array_map(function($match) use ($songMatches, &$idx) {
          $idx++;
          return [
              'id' => strip_tags($songMatches[1][$idx]),
              'name' => strip_tags($songMatches[2][$idx]),
              'artist' => strip_tags($songMatches[3][$idx]),
              'language' => strip_tags($songMatches[4][$idx])
          ];
        }, count($songMatches) > 4 ? $songMatches[1] : [])
    ];
    $expiresAt = Carbon::now()->addMinutes(10);

    $cache->put($cacheKey, $searchResults, $expiresAt);
  }
  return response()->json($searchResults);

});

$app->post('/api/v1/song/lyrics', function(Repository $cache, Request $request) use ($app) {

  $song = [
    'name' => $request->get('name'),
    'artist' => $request->get('artist'),
    'language' => $request->get('language')
  ];

  $api = [
    'english' => [
      'base' => 'http://search.azlyrics.com/search.php?q=', 
     ],
    'japanese' => [
      'base' => 'http://search.j-lyric.net/index.php?',
      'name' => 'kt',
      'artist' => 'ka'
    ]
  ];


  $song['name'] = trim(preg_replace("/\[[^)]+\]/", "", $song['name']));
  switch($song['language']) {
    case 'english':
      // var_dump($song);
      $searchQuery = urlencode(join([
        $song['name'],
        $song['artist']
      ], " "));
      
      $results = file_get_contents($api['english']['base'] . $searchQuery);
      
      preg_match_all("/visitedlyr.*\n.*href=\"(.*)\"\ .*" . $song['name'] . ".*" . $song['artist'] . "/i", $results, $linkMatch);
//        var_dump(count($linkMatch)); 
      if(count($linkMatch) > 0) {
        // var_dump($linkMatch[1]);
        // return $linkMatch[1][0];
        return [
          'success' => true,
          'link' => $linkMatch[1][0]
        ];
      }
      break;
    case 'japanese':
      // print 'NOT DONE YET' . var_dump($song) . "\n";

      break;
    default:
      // print 'Unsupported Language' . var_dump($song);
      break;
  }

  return [
    'success' => false
  ];



});
