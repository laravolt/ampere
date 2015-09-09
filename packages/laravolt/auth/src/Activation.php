<?php
namespace Laravolt\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait Activation
{
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $user = $this->create($request->all());
        $token = $this->createToken($user);

        Mail::send('auth::auth.activation', compact('token'), function($message) use ($user){
            $message->subject(trans('auth::auth.activation_subject'));
            $message->to($user['email']);
        });

        return redirect()->back()->with('info', trans('auth::auth.register_success'));
    }

    public function getActivate($token)
    {
        $userId = DB::table('users_activation')->whereToken($token)->pluck('user_id');

        if (!$userId) {
            abort(404);
        }

        $user = User::findOrFail($userId);
        $user->status = 'active';
        $user->save();

        DB::table('users_activation')->whereToken($token)->delete();

        return redirect()->to('auth/login')->with('success', trans('auth::auth.activation_success'));
    }

    protected function createToken($user)
    {
        $token = md5(uniqid(rand(), true));
        DB::table('users_activation')->insert(['user_id' => $user->getKey(), 'token' => $token]);

        return $token;
    }
}
