<?php namespace Invisnik\LaravelSteamAuth;

use Invisnik\LaravelSteamAuth\LightOpenID;

class SteamAuth implements SteamAuthInterface {

    /**
     * @var LightOpenID
     */
    private $OpenID;

    /**
     * @var string|bool
     */
    public $SteamID = false;

    /**
     * @var string
     */
    public $redirect_url;

    /**
     * @var string
     */
    const OPENID_URL = 'https://steamcommunity.com/openid';

    public function __construct()
    {
        $this->OpenID = new LightOpenID($_SERVER['SERVER_NAME']);
        $this->OpenID->returnUrl = \Config::get('steam-auth.redirect_url');
        $this->OpenID->identity = self::OPENID_URL;
        $this->init();
    }

    /**
     *  Initialization
     */
    private function init()
    {
        if($this->OpenID->mode == 'cancel')
        {
            $this->SteamID = false;
        }
        else if($this->OpenID->mode)
        {
            if($this->OpenID->validate())
            {
                $this->SteamID = basename($this->OpenID->identity);
            }
            else
            {
				$this->SteamID = false;
            }
        }
    }

    /**
     * Checks the steam login
     *
     * @return bool
     */
    public function validate()
    {
        return $this->SteamID ? true : false;
    }

    /**
     * Returns the redirect response to login
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect()
    {
        return redirect($this->url());
    }

    /**
     * Returns the login url
     *
     * @return String
     */
    public function url()
    {
        return $this->OpenID->authUrl();
    }

    /**
     * Returns the steam id
     *
     * @return bool|string
     */
    public function getSteamId(){
        return $this->SteamID;
    }

}