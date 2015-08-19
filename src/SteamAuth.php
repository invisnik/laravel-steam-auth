<?php namespace Invisnik\LaravelSteamAuth;

use Exception;
use Illuminate\Http\Request;

class SteamAuth implements SteamAuthInterface {

    /**
     * @var integer|null
     */
    public $steamId = null;

    /**
     * @var array|null
     */
    public $steamInfo = null;

    /**
     * @var string
     */
    public $authUrl;

    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var string
     */
    const OPENID_URL = 'https://steamcommunity.com/openid/login';

    /**
     * @var string
     */
    const STEAM_INFO_URL = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s';

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUrl = $this->buildUrl(url(\Config::get('steam-auth.redirect_url')));
    }

    /**
     * Checks the steam login
     *
     * @return bool
     */
    public function validate()
    {
        if($this->request->has('openid_assoc_handle') && $this->request->has('openid_signed') && $this->request->has('openid_sig')) {
            $get = $this->request->all();
            try {
                $params = array(
                    'openid.assoc_handle' => $get['openid_assoc_handle'],
                    'openid.signed' => $get['openid_signed'],
                    'openid.sig' => $get['openid_sig'],
                    'openid.ns' => 'http://specs.openid.net/auth/2.0',
                );
                $signed = explode(',', $get['openid_signed']);
                foreach ($signed as $item) {
                    $val = $get['openid_' . str_replace('.', '_', $item)];
                    $params['openid.' . $item] = get_magic_quotes_gpc() ? stripslashes($val) : $val;
                }
                $params['openid.mode'] = 'check_authentication';
                $data = http_build_query($params);
                $context = stream_context_create(array(
                    'http' => array(
                        'method' => 'POST',
                        'header' =>
                            "Accept-language: en\r\n" .
                            "Content-type: application/x-www-form-urlencoded\r\n" .
                            "Content-Length: " . strlen($data) . "\r\n",
                        'content' => $data,
                    ),
                ));
                $result = file_get_contents(self::OPENID_URL, false, $context);
                preg_match("#^http://steamcommunity.com/openid/id/([0-9]{17,25})#", $get['openid_claimed_id'], $matches);
                $this->steamId = is_numeric($matches[1]) ? $matches[1] : 0;
                $this->parseInfo();
                $response = preg_match("#is_valid\s*:\s*true#i", $result) == 1 ? true : false;
            } catch (Exception $e) {
                $response = false;
            }
            if (is_null($response)) {
                throw new Exception('The Steam login request timed out or was invalid');
            }
            return $response;
        }
        else
        {
            return false;
        }
    }

    /**
     * Validates a given URL, ensuring it contains the http or https URI Scheme
     *
     * @param string $url
     * @return bool
     */
    private function validateUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }

    /**
     * Build the Steam login URL
     *
     * @param string $return A custom return to URL
     * @return string
     */
    private function buildUrl($return = null)
    {
        if (!is_null($return)) {
            if (!$this->validateUrl($return)) {
                throw new Exception('The return URL must be a valid URL with a URI Scheme or http or https.');
            }
        }
        else {
            $return = url('/');
        }
        $https = $this->request->server('HTTPS');
        $params = array(
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $return,
            'openid.realm' => (!empty($https) ? 'https' : 'http') . '://' . $this->request->server('HTTP_HOST'),
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        );
        return self::OPENID_URL . '?' . http_build_query($params, '', '&');
    }

    /**
     * Returns the redirect response to login
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect()
    {
        return redirect($this->getAuthUrl());
    }

    /**
     * Get user data from steam api
     *
     * @return void
     */
    public function parseInfo() {
        if(!is_null($this->steamId)) {
            $json = file_get_contents(sprintf(self::STEAM_INFO_URL, \Config::get('steam-auth.api_key'), $this->steamId));
            $json = json_decode($json, TRUE);
            $this->steamInfo = new SteamInfo($json["response"]["players"][0]);
        }
    }

    /**
     * Returns the login url
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    /**
     * Returns the SteamUser info
     *
     * @return SteamInfo
     */
    public function getUserInfo(){
        return $this->steamInfo;
    }

    /**
     * Returns the steam id
     *
     * @return bool|string
     */
    public function getSteamId(){
        return $this->steamId;
    }

}
