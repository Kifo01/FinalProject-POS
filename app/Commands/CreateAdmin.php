<?php
namespace App\Commands;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
class CreateAdmin extends BaseCommand {
    protected $group='POS';
    protected $name='pos:create-admin';
    protected $description='Create the initial production administrator from environment variables.';
    public function run(array $params) {
        $username=trim((string)getenv('POS_ADMIN_USERNAME'));
        $name=trim((string)getenv('POS_ADMIN_NAME'));
        $password=(string)getenv('POS_ADMIN_PASSWORD');
        if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/',$username) || $name==='' || mb_strlen($name)>100 || strlen($password)<10) {
            CLI::error('Set POS_ADMIN_USERNAME, POS_ADMIN_NAME, and POS_ADMIN_PASSWORD (at least 10 characters) in the environment.');
            return;
        }
        $users=new UserModel();
        if ($users->where('username',$username)->first()) { CLI::error('That username already exists.'); return; }
        $users->insert(['username'=>$username,'full_name'=>$name,'password'=>password_hash($password,PASSWORD_DEFAULT),'role'=>'admin','active'=>1,'created_at'=>date('Y-m-d H:i:s')]);
        CLI::write('Administrator created. Remove POS_ADMIN_PASSWORD from the environment.');
    }
}
