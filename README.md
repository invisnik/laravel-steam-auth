# Steam authentication for laravel 5
This package is a Laravel 5 service provider which provides Steam OpenID and is very easy to integrate with any project which requires steam authentication.

## Installation Via Composer
Add this to your composer.json file, in the require object:

```javascript
"invisnik/laravel-steam-auth": "dev-master"
```

After that, run composer install to install the package.

Add the service provider to `app/config/app.php`, within the `providers` array.

```php
'providers' => array(
	// ...
	'Invisnik\LaravelSteamAuth\SteamServiceProvider',
)
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
        $this->steam->Init();

        if( $this->steam->isLoginIn()){
            return  $this->steam->SteamID;  //returns the user steamid
        }else{
            return  $this->steam->redirect(); //redirect to steam login page
        }
	}
}
```
