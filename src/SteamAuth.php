<?php namespace Invisnik\LaravelSteamAuth;

use Invisnik\LaravelSteamAuth\LightOpenID;

class SteamAuth {

    private $OpenID;
    private $OnLoginCallback;
    private $OnLoginFailedCallback;

    public $SteamID = false;

    public function __construct()
    {
        $Server = $_SERVER['SERVER_NAME'];
        $this->OpenID = new LightOpenID($Server);
        $this->OpenID->identity = 'https://steamcommunity.com/openid';

        $this->OnLoginCallback = function(){};
        $this->OnLoginFailedCallback = function(){};
    }

    public function __call($closure, $args)
    {
        return call_user_func_array($this->$closure, $args);
    }

    public function Init()
    {
        if($this->OpenID->mode == 'cancel'){
            $this->OnLoginFailedCallback(function(){
                $this->SteamID = false;
            });
        }else if($this->OpenID->mode){
            if($this->OpenID->validate()){
                $this->SteamID = basename($this->OpenID->identity);
            }else{
                $this->OnLoginFailedCallback(function(){
                    $this->SteamID = false;
                });
            }
        }
    }

    public function isLoginIn()
    {
        return $this->SteamID ? true : false;
    }

    public function redirect()
    {
        return redirect($this->getLoginURL());
    }

    public function getLoginURL()
    {
        return $this->OpenID->authUrl();
    }

    public function SetOnLoginCallback($OnLoginCallback)
    {
        $this->OnLoginCallback = $OnLoginCallback;
    }

    public function SetOnLoginFailedCallback($OnLoginFailedCallback)
    {
        $this->OnLoginFailedCallback = $OnLoginFailedCallback;
    }

}