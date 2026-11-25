<?php
namespace App\Controllers;
use App\Models\UserModel;
class Auth extends BaseController {
    public function home() { return redirect()->to(session('user_id') ? (session('role') === 'admin' ? '/admin' : '/terminal') : '/login'); }
    public function login() { if (session('user_id')) return $this->home(); return view('auth/login'); }
    public function attempt() {
        $username = trim((string)$this->request->getPost('username'));
        $user = (new UserModel())->where('username',$username)->first();
        if (!$user || !$user['active'] || !password_verify((string)$this->request->getPost('password'),$user['password'])) return redirect()->back()->withInput()->with('error','Invalid username or password.');
        session()->regenerate(true);
        session()->set(['user_id'=>$user['id'],'role'=>$user['role'],'full_name'=>$user['full_name']]);
        return $this->home();
    }
    public function logout() { session()->destroy(); return redirect()->to('/login'); }
}
