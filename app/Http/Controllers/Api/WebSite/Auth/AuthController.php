<?php

namespace App\Http\Controllers\Api\WebSite\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Auth\{CheckCodeRequest, ForgetPasswordRequest,
    LoginRequest, RegisterRequest, ResendCodeRequest,
    ResetPasswordRequest, VerifyRequest};

use App\Http\Resources\Api\WebSite\User\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $code = 1111;

            if (setting('use_sms_service') == 'enable') {
                $code = mt_rand(1111, 9999);
                $other_data = ['email_verified_at' => now(), 'is_admin_active_user' => true, 'is_ban' => false, 'user_type' => 'client'];
            }
            else
            {
                $other_data = ['is_active' => true, 'phone_verified_at' => now(), 'email_verified_at' => now(), 'is_admin_active_user' => true, 'is_ban' => false, 'user_type' => 'client'];
            }

            $user = User::create(Arr::except($request->validated(), ['country_id', 'city_id', 'company_name', 'company_address', 'commercial_register_num', 'tax_card_num', 'categories']) + $other_data);
            $user->profile()->create(Arr::only($request->validated(), ['country_id', 'city_id']));
            $user->company()->create(Arr::only($request->validated(), ['company_name', 'company_address', 'commercial_register_num', 'tax_card_num']));
            $user->categories()->attach($request->validated('categories'));

            $user = $user->fresh();
            $is_verified = $user->is_active && $user->phone_verified_at;

            DB::commit();
            return response()->json(['status' => true, 'data' => ['is_verified' => $is_verified], 'message' => trans('website.auth.success_sign_up')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.auth.not_registered_try_again')], 422);
        }
    }

    public function verify(VerifyRequest $request)
    {
        $user = User::where(['verified_code' => $request->code, 'phone' => $request->phone, 'phone_code' => $request->phone_code, 'user_type' => 'client'])->whereNull('phone_verified_at')->firstOrFail();
        DB::beginTransaction();
        try {
            if ($user->verified_code == $request->code) {
                $user->update(['is_active' => true, 'verified_code' => null, 'phone_verified_at' => now()]);
                $token = JWTAuth::fromUser($user);
                data_set($user, 'token', $token);
                DB::commit();
                return response()->json(['status' => true, 'message' => '', 'data' => new UserResource($user)], 200);
            } else {
                return response()->json(['status' => false, 'message' => trans('website.auth.wrong_code_please_try_again'), 'data' => null], 422);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    public function resendCode(ResendCodeRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code, 'user_type' => 'client'])->firstOrFail();
        try {
            if (setting('use_sms_service') == 'enable') {
                $code = mt_rand(1111, 9999);
                $user->update(['verified_code' => $code]);
            }
            return response()->json(['status' => true, 'data' => null, 'message' => trans('website.auth.sent_code_successfully')], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    public function login(LoginRequest $request)
    {
        if (! $token = auth('api')->attempt($this->getCredentials($request), ['user_type' => 'client'])) {
            return response()->json(['status' => false, 'data' => null, 'is_active' => false, 'is_ban' => false, 'message' => trans('website.auth.failed')], 402);
        }

        $user = auth('api')->user();

        if (! $user->is_active) {
            if (setting('use_sms_service') == 'enable') {
                $code = mt_rand(1111, 9999);
                $user->update(['verified_code' => $code]);
            }
            auth('api')->logout();
            return response()->json(['status' => false, 'data' => null, 'is_active' => (bool) $user->is_active, 'is_ban' => (bool) $user->is_ban, 'message' => trans('website.auth.account_is_not_activated')], 403);
        } elseif ($user->is_ban) {
            auth('api')->logout();
            return response()->json(['status' => false, 'data' => null, 'is_active' => (bool) $user->is_active, 'is_ban' => (bool) $user->is_ban, 'message' => trans('website.auth.account_banned_by_admin', ['ban_reason' => $user->ban_reason])], 403);
        }

        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            auth('api')->logout();
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.auth.Trying_to_sign_up_for_admin_account')], 403);
        }

        $user->profile()->update(['last_login_at' => now()]);
        data_set($user, 'token', $token);
        return (new UserResource($user))->additional(['status' => true, 'message' => '']);
    }

    public function forgotPassword(ForgetPasswordRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code, 'user_type' => 'client'])->firstOrFail();
        try {
            if (setting('use_sms_service') == 'enable') {
                $code = mt_rand(1111, 9999);
                $user->update(['verified_code' => $code]);
            }

            return response()->json(['status' => true, 'message' => trans('website.auth.sent_code_successfully'), 'data' => ['code' => $user->reset_code]], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => trans('website.messages.something_went_wrong_please_try_again'), 'data' => null], 422);
        }
    }

    public function checkCode(CheckCodeRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code, 'user_type' => 'client'])->firstOrFail();
        if (!$user) {
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.auth.user_not_found')], 404);
        } elseif (!$user->phone_verified_at && $user->verified_code == $request->code) {
            return response()->json(['status' => true, 'data' => null, 'message' => trans('website.auth.code_is_true'), 'is_active' => false]);
        }
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.auth.first_verify_account'), 'is_active' => true]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code, 'user_type' => 'client'])->firstOrFail();

        if (!$user) {
            return response()->json(['status' => false, 'data' => null, 'message' => trans('website.auth.phone_not_true_or_account_deactive')], 422);
        } elseif (!$user->phone_verified_at && $user->verified_code == $request->code) {
            $user->update(['password' => $request->password, 'verified_code' => null, 'is_active' => true, 'phone_verified_at' => now()]);
        }

        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.auth.success_change_password')]);
    }

    public function logout()
    {
        if (auth('api')->check()) {
            $user = auth('api')->user();
            $user->profile()->update(['last_login_at' => null]);
            auth('api')->logout();

            return response()->json(['status' => true, 'data' => null, 'message' => trans('website.auth.signed_out_successfully')]);
        }
    }

    protected function getCredentials(Request $request)
    {
        $username = $request->username;
        $credentials = [];

        switch ($username) {
            case filter_var($username, FILTER_VALIDATE_EMAIL):
                $username = 'email';
                break;
            case is_numeric($username):
                $username = 'phone';
                break;
            default:
                $username = 'email';
                break;
        }

        $credentials[$username] = $request->username;

        if ($request->password) {
            $credentials['password'] = $request->password;
        }

        return $credentials;
    }
}
