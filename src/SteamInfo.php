<?php namespace Invisnik\LaravelSteamAuth;

class SteamInfo
{
    protected $steamID64;
    protected $nick;
    protected $lastLogin;
    protected $profileURL;
    protected $profilePicture;
    protected $profilePictureMedium;
    protected $profilePictureFull;
    protected $name;
    protected $clanID;
    protected $createdAt;
    protected $countryCode;

    function __construct($data)
    {
        $this->steamID64            = isset($data["steamid"]) ? $data["steamid"] : null;
        $this->nick                 = isset($data["personaname"]) ? $data["personaname"] : null;
        $this->lastLogin            = isset($data["lastlogoff"]) ? $data["lastlogoff"] : null;
        $this->profileURL           = isset($data["profileurl"]) ? $data["profileurl"] : null;
        $this->profilePicture       = isset($data["avatar"]) ? $data["avatar"] : null;
        $this->profilePictureMedium = isset($data["avatarmedium"]) ? $data["avatarmedium"] : null;
        $this->profilePictureFull   = isset($data["avatarfull"]) ? $data["avatarfull"] : null;
        $this->name                 = isset($data["realname"]) ? $data["realname"] : null;
        $this->clanID               = isset($data["primaryclanid"]) ? $data["primaryclanid"] : null;
        $this->createdAt            = isset($data["timecreated"]) ? $data["timecreated"] : null;
        $this->countryCode          = isset($data["loccountrycode"]) ? $data["loccountrycode"] : null;
    }

    /**
     * @return mixed
     */
    public function getSteamID64()
    {
        return $this->steamID64;
    }

    /**
     * Converts SteamID64 to SteamID32
     * @return string
     */
    public function getSteamID()
    {
        //See if the second number in the steamid (the auth server) is 0 or 1. Odd is 1, even is 0
        $authserver = bcsub($this->steamID64, '76561197960265728') & 1;
        //Get the third number of the steamid
        $authid = (bcsub($this->steamID64, '76561197960265728') - $authserver) / 2;

        //Concatenate the STEAM_ prefix and the first number, which is always 0, as well as colons with the other two numbers
        return "STEAM_0:$authserver:$authid";
    }

    /**
     * @return mixed
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * @return mixed
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @return mixed
     */
    public function getProfileURL()
    {
        return $this->profileURL;
    }

    /**
     * @return mixed
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @return mixed
     */
    public function getProfilePictureMedium()
    {
        return $this->profilePictureMedium;
    }

    /**
     * @return mixed
     */
    public function getProfilePictureFull()
    {
        return $this->profilePictureFull;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getClanID()
    {
        return $this->clanID;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
}
