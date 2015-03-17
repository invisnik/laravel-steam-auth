<?php namespace Invisnik\LaravelSteamAuth;

use Invisnik\LaravelSteamAuth\LightOpenID;

class SteamAuth implements SteamAuthInterface {

    private $OpenID;

    public $SteamID = false;

    public function __construct()
    {
        $server = $_SERVER['SERVER_NAME'];
        $this->OpenID = new LightOpenID($server);
        $this->OpenID->identity = 'https://steamcommunity.com/openid';
        $this->init();
    }

    private function init()
    {
        if($this->OpenID->mode == 'cancel'){

            $this->SteamID = false;

        }else if($this->OpenID->mode){

            if($this->OpenID->validate()){

                $this->SteamID = basename($this->OpenID->identity);

            }else{

				$this->SteamID = false;

            }

        }
    }

    public function validate()
    {
        return $this->SteamID ? true : false;
    }

    public function redirect()
    {
        return redirect($this->url());
    }

    public function url()
    {
        return $this->OpenID->authUrl();
    }

    public function getSteamId(){
        return $this->SteamID;
    }

}