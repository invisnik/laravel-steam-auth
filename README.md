# Steam authentication for Laravel 5
[![Code Climate](https://codeclimate.com/github/invisnik/laravel-steam-auth/badges/gpa.svg)](https://codeclimate.com/github/invisnik/laravel-steam-auth)
[![Latest Stable Version](https://img.shields.io/packagist/v/invisnik/laravel-steam-auth.svg)](https://packagist.org/packages/invisnik/laravel-steam-auth)
[![Total Downloads](https://img.shields.io/packagist/dt/invisnik/laravel-steam-auth.svg)](https://packagist.org/packages/invisnik/laravel-steam-auth)
[![License](https://img.shields.io/github/license/invisnik/laravel-steam-auth.svg)](https://packagist.org/packages/invisnik/laravel-steam-auth)

This package is a Laravel 5 service provider which provides support for Steam OpenID and is very easy to integrate with any project that requires Steam authentication.

## Installation Via Composer
Add this to your `composer.json` file, in the require object:

```javascript
"invisnik/laravel-steam-auth": "3.*"
```

After that, run `composer install` to install the package.

#### Laravel 5.4 and below

Add the service provider to `app/config/app.php`, within the `providers` array.

```php
'providers' => [
	// ...
	Invisnik\LaravelSteamAuth\SteamServiceProvider::class,
]
```

The package is automatically added if you are in Laravel 5.5.

#### Steam API Key

Add your Steam API key to your `.env` file. You can your API key [here](http://steamcommunity.com/dev/apikey).

```
STEAM_API_KEY=SomeKindOfAPIKey
```

#### Config Files

Lastly, publish the config file.

```
php artisan vendor:publish
```
## Usage example
In `config/steam-auth.php`:
```php
return [

    /*
     * Redirect URL after login
     */
    'redirect_url' => '/login',
    /*
     *  API Key (set in .env file) [http://steamcommunity.com/dev/apikey]
     */
    'api_key' => env('STEAM_API_KEY', ''),
    /*
     * Is using https?
     */
    'https' => false
];

```
In `routes/web.php`:
```php
Route::get('login', 'AuthController@login')->name('login');
```
**Note:** if you want to keep using Laravel's default logout route, add the following as well:
```php
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
```
In `AuthController`
```php
namespace App\Http\Controllers;

use Invisnik\LaravelSteamAuth\SteamAuth;
use App\User;
use Auth;

class AuthController extends Controller
{
    /**
     * @var SteamAuth
     */
    private $steam;

    public function __construct(SteamAuth $steam)
    {
        $this->steam = $steam;
    }

    public function login()
    {
        if ($this->steam->validate()) {
            $info = $this->steam->getUserInfo();
            if (!is_null($info)) {
                $user = User::where('steamid', $info->steamID64)->first();
                if (is_null($user)) {
                    $user = User::create([
                        'username' => $info->personaname,
                        'avatar'   => $info->avatarfull,
                        'steamid'  => $info->steamID64
                    ]);
                }
            	Auth::login($user, true);
            	return redirect('/'); // redirect to site
            }
        }
        return $this->steam->redirect(); // redirect to Steam login page
    }
}

```
