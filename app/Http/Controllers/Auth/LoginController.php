<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Rules\ReCaptcha;


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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }

    public function logout()
    {
        $this->businessUtil->activityLog(auth()->user(), 'logout');

        request()->session()->flush();
        \Auth::logout();

        return redirect('/login');
    }

    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $this->businessUtil->activityLog($user, 'login', null, [], false, $user->business_id);

        if (! $user->business->is_active) {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
              );
        } elseif ($user->status != 'active') {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
              );
        } elseif (! $user->allow_login) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
                );
        } elseif (($user->user_type == 'user_customer') && ! $this->moduleUtil->hasThePermissionInSubscription($user->business_id, 'crm_module')) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.business_dont_have_crm_subscription')]
                );
        }
    }

    protected function redirectTo()
    {
        $user = auth()->user();
        
        // If user is not authenticated, go to business selection
        if (!$user) {
            return '/business/select';
        }
        
        // If user has no business assigned, they need to select/register one
        if (!$user->business_id) {
            return '/business/select';
        }
        
        // If user has a business but it's inactive, go to business selection
        if ($user->business && !$user->business->is_active) {
            return '/business/select';
        }
        
        // If user has an active business, check their role
        if ($user->business_id && $user->business && $user->business->is_active) {
            // Check if user is a cashier or has POS-only access
            $userRoles = $user->getRoleNames();
            $isCashier = $userRoles->contains(function ($role) {
                return str_contains(strtolower($role), 'cashier') || str_contains(strtolower($role), 'pos');
            });
            
            // Check if user has limited permissions (only POS access)
            $hasLimitedAccess = !$user->can('superadmin') && 
                               !$user->can('admin') && 
                               ($user->can('sell.create') || $user->can('pos.create'));
            
            // Set up business session data before redirecting
            $business = $user->business;
            session(['selected_business_id' => $business->id]);
            session([
                'business' => [
                    'id' => $business->id,
                    'name' => $business->name,
                    'currency_id' => $business->currency_id,
                    'start_date' => $business->start_date,
                    'enabled_modules' => $business->enabled_modules,
                    'currency_precision' => $business->currency_precision ?? 2,
                    'quantity_precision' => $business->quantity_precision ?? 2,
                    'time_zone' => $business->time_zone ?? 'UTC',
                    'date_format' => $business->date_format ?? 'd/m/Y',
                    'time_format' => $business->time_format ?? 24,
                    'currency_symbol_placement' => $business->currency_symbol_placement ?? 'before',
                    'ref_no_prefixes' => $business->ref_no_prefixes ?? [],
                    'pos_settings' => $business->pos_settings ?? [],
                ]
            ]);
            
            // Redirect cashiers or users with limited access directly to POS
            if ($isCashier || $hasLimitedAccess) {
                return '/pos/create';
            }
            
            // Redirect admin users to home/dashboard
            return '/home';
        }
        
        // Default fallback to business selection
        return '/business/select';
    }

    public function validateLogin(Request $request)
    {
        if(config('constants.enable_recaptcha')){
            $this->validate($request, [
                $this->username() => 'required|string',
                'password' => 'required|string',
                'g-recaptcha-response' => ['required', new ReCaptcha]
            ]);
        }else{
            $this->validate($request, [
                $this->username() => 'required|string',
                'password' => 'required|string',
            ]);
        }
       
    }

}
