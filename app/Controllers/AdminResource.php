<?php
namespace App\Controllers;
use App\Services\ImageUpload;
abstract class AdminResource extends BaseController {
    protected string $kind;
    protected string $modelClass;
    protected array $fields;
    protected function model() { return new $this->modelClass(); }
    public function index() { return view('admin/list',['kind'=>$this->kind,'rows'=>$this->model()->orderBy('id','DESC')->findAll()]); }
    public function form(?int $id=null) {
        $row=$id ? $this->model()->find($id) : null;
        if ($id && !$row) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return view('admin/form',['kind'=>$this->kind,'row'=>$row]);
    }
    public function save(?int $id=null) {
        $model=$this->model(); $old=$id ? $model->find($id) : null;
        if ($id && !$old) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $input=[]; foreach ($this->fields as $field) $input[$field]=trim((string)$this->request->getPost($field));
        try {
            $this->validateInput($input,$id,$old);
            $fileField=$this->kind==='products'?'image':($this->kind==='staff'?'avatar':null);
            if ($fileField) $input[$fileField]=ImageUpload::store($this->request->getFile($fileField),$this->kind,$old[$fileField] ?? null);
            if ($this->kind==='staff') {
                $password=(string)$this->request->getPost('password');
                if ($password!=='') $input['password']=password_hash($password,PASSWORD_DEFAULT);
            }
            if (!$id) { $input['active']=1; $input['created_at']=date('Y-m-d H:i:s'); }
            if ($id) $model->update($id,$input); else $model->insert($input);
            if ($fileField && $old && !empty($old[$fileField]) && $input[$fileField] !== $old[$fileField] && str_starts_with($old[$fileField],'uploads/'.$this->kind.'/')) @unlink(FCPATH.$old[$fileField]);
            return redirect()->to('/admin/'.$this->kind)->with('success','Saved successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error',$e instanceof \DomainException || $e instanceof \RuntimeException ? $e->getMessage() : 'Could not save this record.');
        }
    }
    abstract protected function validateInput(array $input, ?int $id, ?array $old): void;
    public function archive(int $id) {
        $model=$this->model(); $row=$model->find($id);
        if (!$row) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        if ($this->kind==='staff' && $row['role']==='admin' && $row['active'] && $model->where('role','admin')->where('active',1)->countAllResults()<=1) return redirect()->back()->with('error','The last active admin cannot be deactivated.');
        if ($this->kind==='staff' && (int)$id===(int)session('user_id')) return redirect()->back()->with('error','Deactivate your account from another admin account.');
        $model->update($id,['active'=>0]); return redirect()->back()->with('success','Archived successfully.');
    }
}
