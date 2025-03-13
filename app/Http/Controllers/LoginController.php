<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function processLogin(Request $request)
    {
      $request->validate([
          'user_id' => 'required',
          'password' => 'required',
      ]);

      // 從 users 資料表查找對應的 user_id
      $user = DB::selectOne("SELECT * FROM users WHERE user_id = ?", [$request->user_id]);
    
      if(!isset($user)){
        return redirect()->route('login')->with('error', '沒有該使用者ID');
      }elseif($user && isset($user->password) && password_verify($request->password, $user->password)){
        session([
          'user_id'   => $user->user_id,
          'client_id' => $user->client_id,
          'name'      => $user->name
        ]);
        return redirect()->route('dashboard');
      }else{
        return redirect()->route('login')->with('error', '密碼錯誤');
      }
    }

    public function logout()
    {
        session()->flush(); // 清除 Session
        return redirect()->route('login');
    }
}
