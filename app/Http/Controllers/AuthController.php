<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private $px;
    function __construct(){
      $this->px=DB::getTablePrefix();
    }
    
    public function auth(Request $f){
        $username=$f->txtUsername;
        $password=$f->txtPassword;     
        
        //$px=DB::getTablePrefix();

       $u=DB::select("select * from {$this->px}users where (username='$username' or email='$username') and password='$password'");   
      
       if(count($u)==1){             

          $session_id=session()->getId();  
          
          session(['sess_id'=>$session_id,
                   'sess_user_id'=>$u[0]->id,
                   'sess_user_name' =>$u[0]->username,
                   'sess_email'=>$u[0]->email
                   ]); 
                         
          //return view("test.test_view");
          //return redirect("home");
          return Redirect::route('home');

      }else{
        return redirect("/");
       //  echo "Username or Password is incorrect";  
      }
        
       // return Redirect::route('home');
     }

     public function logout(){
        session()->forget(['sess_id', 'sess_user_id','sess_user_name','sess_email']);
        session()->flush();
        session()->regenerate();  
        return redirect("/");    
      }

}
