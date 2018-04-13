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
    'redirect_url' => '/auth/steam/handle',
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
Route::get('auth/steam', 'AuthController@redirectToSteam')->name('auth.steam');
Route::get('auth/steam/handle', 'AuthController@handle')->name('auth.steam.handle');
```
**Note:** if you want to keep using Laravel's default logout route, add the following as well:
```php
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
```
In `AuthController`:
```php
namespace App\Http\Controllers;

use Invisnik\LaravelSteamAuth\SteamAuth;
use App\User;
use Auth;

class AuthController extends Controller
{
    /**
     * The SteamAuth instance.
     *
     * @var SteamAuth
     */
    protected $steam;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $redirectURL = '/';

    /**
     * AuthController constructor.
     * 
     * @param SteamAuth $steam
     */
    public function __construct(SteamAuth $steam)
    {
        $this->steam = $steam;
    }

    /**
     * Redirect the user to the authentication page
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectToSteam()
    {
        return $this->steam->redirect();
    }

    /**
     * Get user info and log in
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handle()
    {
        if ($this->steam->validate()) {
            $info = $this->steam->getUserInfo();

            if (!is_null($info)) {
                $user = $this->findOrNewUser($info);

                Auth::login($user, true);

                return redirect($this->redirectURL); // redirect to site
            }
        }
        return $this->redirectToSteam();
    }

    /**
     * Getting user by info or created if not exists
     *
     * @param $info
     * @return User
     */
    protected function findOrNewUser($info)
    {
        $user = User::where('steamid', $info->steamID64)->first();

        if (!is_null($user)) {
            return $user;
        }

        return User::create([
            'username' => $info->personaname,
            'avatar' => $info->avatarfull,
            'steamid' => $info->steamID64
        ]);
    }
}

```

Should you wish to use a login redirection URL that is differant from the one you specified in the config

```php
// Inside your controller login method
$this->steam->setRedirectUrl(route('login.route'));

...

return $this->steam->redirect();
```

If you need another steamID you can use another package to convert the given steamID64 to another type like [xPaw/SteamID](https://github.com/xPaw/SteamID.php).