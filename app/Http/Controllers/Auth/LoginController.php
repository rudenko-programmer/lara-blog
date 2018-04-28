<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\SocialAccount;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $user = $this->createOrGetUser($provider);

        /* Log in the user */
        Auth::login( $user );

        /* Redirect to the app */
        return redirect('/');
        
    }


    public function createOrGetUser($provider)
    {
        $providerUser = Socialite::driver($provider)->user();
        $account = SocialAccount::whereProvider($provider)
            ->whereProviderId($providerUser->getId())
            ->first();          

        if ($account) {
            return $account->user;
        } else {
            
            $user = User::whereEmail($providerUser->getEmail())->first();
                        
            if (!$user) {
                $login = $providerUser->getNickname();
                if($login == ''){
                    $parts = explode("@", $providerUser->getEmail());
                    $loginNative = $login = $parts[0];

                    $counter = 1;
                    while (User::whereLogin($login)->first()){
                        $login = $loginNative."_".$counter;
                        $counter++;
                    }
                }

                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $login,
                    'password' => bcrypt(random_bytes(10)),
                ]);
            }

            $account = new SocialAccount([
                'user_id' => $user->id,
                'provider_id' => $providerUser->getId(),
                'provider' => $provider
            ]);       

            $account->save();

            return $user;
        }

    }
}
