<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PerfilController extends Controller
{
    public function index(){
        return view('perfil.index');
    }

    public function store(Request $request){
        $request->request->add(['username' => Str::slug($request->username)]);
        $validated = $request->validate([
            'username' => ['required','unique:users,username,'. auth()->user()->id,'min:3','max:20', 'not_in:twitter,editar-perfil'],
        ]);

        if($request->imagen){
            // dd($request->file('imagen'));
            $manager = new ImageManager(new Driver());
            $imagen = $request->file('imagen');
    
            $nombreImagen = Str::uuid() . "." . $imagen->extension();
    
            $imagenServidor = $manager->read($imagen);
            $imagenServidor->scale(500,500);
    
            $imagenPath = public_path('perfiles') . '/' . $nombreImagen;
            $imagenServidor->save($imagenPath);


        }

        //Guardar cambios
        $user = User::find(auth()->user()->id);
        $user->username = $request->username;
        $user->imagen = $nombreImagen ?? auth()->user()->imagen ?? '';
        $user->save();

        //Redireccionar usuario
        return redirect()->route('posts.index',$user->username);
    }
}
