<?php

namespace Presentation\Http\Middleware;

use Application\Interfaces\CurrentUserServiceInterface;
use Closure;
use Exception;
use Domain\Entities\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    private CurrentUserServiceInterface $currentUserService;

    public function __construct(
        AuthFactory $auth,
        CurrentUserServiceInterface $currentUserService
    ) {
        parent::__construct($auth);
        $this->currentUserService = $currentUserService;
    }

    /*
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @param  string[]  ...$guards
    * @return mixed
    *
    * @throws \Illuminate\Auth\AuthenticationException
    */
   public function handle($request, Closure $next, ...$guards)
   {
       $this->authenticate($request, $guards);

       /*
        * @var User $user
        */
        $user = $request->user();

       if (is_null($user)) {
           throw new Exception('User does not exist, please authenticate');
       }

       $this->currentUserService->initialize($user);

       return $next($request);
   }

    protected function redirectTo($request)
    {
        if (!Auth::guard('web')->user()) {
            return route('client');
        }
    }
}
