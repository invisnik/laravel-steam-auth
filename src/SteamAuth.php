<?php namespace Invisnik\LaravelSteamAuth;

use Invisnik\LaravelSteamAuth\LightOpenID;

class SteamAuth implements SteamAuthInterface {

    private $OpenID;

    public $SteamID = false;

    public $redirect_url;

    public function __construct()
    {
        $this->redirect_url = \Config::get('steam-auth.redirect_url') ? \Config::get('steam-auth.redirect_url') :  $_SERVER['SERVER_NAME'];
        $this->OpenID = new LightOpenID($this->redirect_url);
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