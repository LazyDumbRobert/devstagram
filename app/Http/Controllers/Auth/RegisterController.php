<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    //
    public function index() {

        return view('auth.register');
    }
    public function store(Request $request) {
        
        //Se puede modificar el request (en este caso no es necesario por eso esta comentado)
        // $request->request->add(['username' =>Str::slug($request->username) ]);

        //Validación
        $validated = $request->validate([
            'name' => 'required|max:30',
            'username' => 'required|unique:users|min:3|max:20',
            'email' => 'required|unique:users|email|max:60',
            'password' => 'required|confirmed|min:6',
        ]);
        
        User::create([
            'name' => $request->name,
            'username' => Str::slug($request->username),
            'email' => $request->email,
            'password' => $request->password
        ]);

        //Autenticar un usuario
        // auth()->attempt([
        //     'email' => $request->email,
        //     'password' => $request->password,
        // ]);

        //Otra forma de autenticar
        auth()->attempt($request->only('email','password'));

        //Redireccionar al usuario
        return redirect()->route('posts.index');
    }

    public function autenticar(){
        return view('auth.register');
    }
}
