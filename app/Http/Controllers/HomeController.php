<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FirebaseJWT\JWT;

class HomeController extends Controller
{
    public function index()
    {
    	return view('home');
    }

    public function video_call(Request $request)
    {
    	$this->data['access_token'] = $this->getAccessToken($request->get('userId'));

    	return view('video_call', $this->data);
    }

    public function getAccessToken($id)
    {
    	$apiKeySid = 'SKyPgvrhyScZAD7brOU9YALrxvHP0iqOne';
		$apiKeySecret = "aWhxVjBNYW5aMk1odFc4OW4ycWpNR2JWODA5aU5RQzY=";

		$now = time();
		$exp = $now + 3600;

		$username = $id;

		if(!$username){
			$jwt = '';
		}else {
			$header = array('cty' => "stringee-api;v=1");
			$payload = array(
			    "jti" => $apiKeySid . "-" . $now,
			    "iss" => $apiKeySid,
			    "exp" => $exp,
			    "userId" => $username
			);

			$jwt = JWT::encode($payload, $apiKeySecret, 'HS256', null, $header);
		}

		$res = array(
			'access_token' => $jwt
			);

		header('Access-Control-Allow-Origin: *');
		
		return $jwt;
    }
}
