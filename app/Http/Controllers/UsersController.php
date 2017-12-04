<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;

class UsersController extends Controller
{
    //权限设置
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index']
        ]);

        //只允许未登录用户访问注册页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    //注册页面控制器：用户名、邮箱、密码
    public function store(Request $request)
    {
        //验证
        $this->validate($request, [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        //验证通过后，添加到数据库
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);


        //创建成功后的路由，及欢迎信息
        // session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    //用户资料页面编辑
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //用户资料更新动作
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.edit', $user->id);
    }

    public function index()
    {
        //$users = User::all();所有用户
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    //删除用户控制器
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

}
