<?php namespace Invisnik\LaravelSteamAuth;

use Illuminate\Support\Fluent;

class SteamInfo extends Fluent
{

    /**
     * {@inheritdoc}
     */
    public function __construct($data)
    {
        $steamID = isset($data['steamid']) ? $data['steamid'] : null;
        unset($data['steamid']);

        parent::__construct($data);

        $this->attributes['steamID64'] = $steamID;
        $this->attributes['steamID'] = $this->getSteamID($steamID);
    }

    /**
     * Get SteamID
     *
     * @param  string $value
     * @return string
     */
    public function getSteamID($value)
    {
        if (is_null($value)) return '';

        //See if the second number in the steamid (the auth server) is 0 or 1. Odd is 1, even is 0
        $authserver = bcsub($value, '76561197960265728') & 1;
        //Get the third number of the steamid
        $authid = (bcsub($value, '76561197960265728') - $authserver) / 2;

        //Concatenate the STEAM_ prefix and the first number, which is always 0, as well as colons with the other two numbers
        return "STEAM_0:$authserver:$authid";
    }
}
