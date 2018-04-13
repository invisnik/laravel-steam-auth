<?php

namespace Invisnik\LaravelSteamAuth;

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
    }
}
