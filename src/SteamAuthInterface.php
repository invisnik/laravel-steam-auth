<?php
namespace Invisnik\LaravelSteamAuth;

interface SteamAuthInterface
{
    public function url();
    public function redirect();
    public function validate();
    public function getSteamId();
}