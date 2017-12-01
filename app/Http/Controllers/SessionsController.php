<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;//引入Laravel认证机制

class SessionsController extends Controller
{
    //创建用户登录会话
    public function create()
    {
        return view('sessions.create');
    }

    //登录过程
    public function store(Request $request)
    {
       $credentials = $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required'
       ]);

       if (Auth::attempt($credentials, $request->has('remember'))) {
           // 登录成功后的相关操作
           session()->flash('success', '欢迎回来！');
           return redirect()->route('users.show', [Auth::user()]);
       } else {
           // 登录失败后的相关操作
           session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
           return redirect()->back();
       }

       return;
    }

    //退出过程
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
