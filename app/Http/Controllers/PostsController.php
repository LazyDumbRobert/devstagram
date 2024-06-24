<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PostsController extends Controller implements HasMiddleware
{   
    public function __construct()
    {
        $this->middleware('auth');
    }
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index','show']),
        ];
    }

    public function index(User $user){
       
        $posts = Post::where(['user_id' => $user->id])->latest()->paginate(3);
        return view('dashboard',[
            'user' => $user,
            'posts' => $posts
        ]);
    }

    public function create(){
        return view('posts.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'imagen' => 'required'
        ]);

        // Post::create([
        //     'titulo' => $request->titulo,
        //     'descripcion' => $request->descripcion,
        //     'imagen' => $request->imagen,
        //     'user_id' => auth()->user()->id

        // ]);

        //Otra forma
        // $post = new Post;
        // $post->titulo =  $request->titulo;
        // $post->descripcion =  $request->descripcion;
        // $post->imagen =  $request->imagen;
        // $post->user_id =  auth()->user()->id;

        $request->user()->posts()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
            'user_id' => auth()->user()->id
        ]);

        return redirect()->route('posts.index',auth()->user()->username);
    }

    public function show( User $user, Post $post,){
        
        return view('posts.show',['post' => $post,'user' => $user]);
    }

    public function destroy(Post $post){
        Gate::allows('delete',$post);
        $post->delete();

        //Eliminar la imagen
        $imagenPath = public_path('uploads/' . $post->imagen);

        if(File::exists($imagenPath)){
            unlink($imagenPath);
        }

        return redirect()->route('posts.index',auth()->user()->username);
    }
}
