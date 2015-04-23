# Steam authentication for laravel 5
[![Latest Stable Version](https://poser.pugx.org/invisnik/laravel-steam-auth/v/stable.svg)](https://packagist.org/packages/invisnik/laravel-steam-auth) 
[![Total Downloads](https://poser.pugx.org/invisnik/laravel-steam-auth/downloads)](https://packagist.org/packages/invisnik/laravel-steam-auth)
[![License](https://poser.pugx.org/invisnik/laravel-steam-auth/license.svg)](https://packagist.org/packages/invisnik/laravel-steam-auth)

This package is a Laravel 5 service provider which provides Steam OpenID and is very easy to integrate with any project which requires steam authentication.

## Installation Via Composer
Add this to your composer.json file, in the require object:

```javascript
"invisnik/laravel-steam-auth": "1.1.*"
```

After that, run composer install to install the package.

Add the service provider to `app/config/app.php`, within the `providers` array.

```php
'providers' => array(
	// ...
	'Invisnik\LaravelSteamAuth\SteamServiceProvider',
)
```

```php
'aliases' => array(
	// ...
	'SteamAuth' => 'Invisnik\LaravelSteamAuth\Facades\SteamAuth',
)
```
You how have access to the `SteamAuth` facade.

Lastly, publish the config file.
```
php artisan vendor:publish
```
## Usage
```php
use Invisnik\LaravelSteamAuth\SteamAuth;

class SteamController extends Controller {

    /**
     * @var SteamAuth
     */
    private $steam;

    public function __construct(SteamAuth $steam)
    {
        $this->steam = $steam;
    }

	public function getLogin()
	{

        if( $this->steam->validate()){
            return  $this->steam->getSteamId();  //returns the user steamid
        }else{
            return  $this->steam->redirect(); //redirect to steam login page
        }
	}
}
```
