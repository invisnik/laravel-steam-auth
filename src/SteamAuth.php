<?php namespace Invisnik\LaravelSteamAuth;


class SteamAuth implements SteamAuthInterface {

    /**
     * @var string|null
     */
    public $steam_id = null;

    /**
     * @var string
     */
    public $auth_url;

    /**
     * @var string
     */
    const OPENID_URL = 'https://steamcommunity.com/openid/login';

    public function __construct()
    {
        $this->auth_url = $this->buildUrl(\Config::get('steam-auth.redirect_url'));
    }

    /**
     * Checks the steam login
     *
     * @return bool
     */
    public function validate()
    {
        if(isset($_GET['openid_assoc_handle']) && isset($_GET['openid_signed']) && isset($_GET['openid_sig'])) {
            try {
                $params = array(
                    'openid.assoc_handle' => $_GET['openid_assoc_handle'],
                    'openid.signed' => $_GET['openid_signed'],
                    'openid.sig' => $_GET['openid_sig'],
                    'openid.ns' => 'http://specs.openid.net/auth/2.0',
                );
                $signed = explode(',', $_GET['openid_signed']);
                foreach ($signed as $item) {
                    $val = $_GET['openid_' . str_replace('.', '_', $item)];
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
                preg_match("#^http://steamcommunity.com/openid/id/([0-9]{17,25})#", $_GET['openid_claimed_id'], $matches);
                $this->steam_id = is_numeric($matches[1]) ? $matches[1] : 0;
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
            $return = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        }
        $params = array(
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $return,
            'openid.realm' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'],
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
     * Returns the login url
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->auth_url;
    }

    /**
     * Returns the steam id
     *
     * @return bool|string
     */
    public function getSteamId(){
        return $this->steam_id;
    }

}