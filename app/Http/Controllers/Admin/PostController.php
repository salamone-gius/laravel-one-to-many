<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// importo la classe helper (prima dei modelli) che ha molti metodi per le stringhe che possono tornare utili, tipo per la generazione dello slug (store())
use Illuminate\Support\Str;

// importo il model di riferimento
use App\Post;

// collegate le due tabelle, importo anche il modello delle categorie
use App\Category;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // passo tutti i post alla variabile $posts
        $posts = Post::all();

        //restituisce la view con la lettura di tutti i post
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // importo tutte le categorie
        $categories = Category::all();

        // restituisco la view del create e le categorie compattate
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // valido i dati che arrivano dal form del create
        $request->validate([
            // passo al metodo validate() un array associativo in cui la chiave sarà il dato che devo controllare e come valore le caratteristiche che quel dato deve avere per poter "passare" la validazione (vedi doc: validation)
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:65535',
            'published' => 'sometimes|accepted',
        ]);

        // prendo i dati dalla request
        $data = $request->all();

        // istanzio il nuovo post
        $newPost = new Post();

        // lo fillo attraverso il mass assignment che avrò già abilitato nel model Post
        $newPost->fill($data);

        // lo slug sarà il risultato del metodo getSlug() dove gli passo il title
        $newPost->slug = $this->getSlug($newPost->title);

        // devo settare la checkbox in modo che restituisca un valore booleano (di default la checkbox restituisce "on" se è checkata e lo devo trasformare in "true")
        // il metodo isset() restituisce true o false. In questo caso "se esiste" restituisce true, altrimenti false
        $newPost->published = isset($data['published']);

        // salvo i dati a db
        $newPost->save();

        // reindirizzo alla rotta che mi restituisce la view del post appena creato 
        return redirect()->route('admin.posts.show', $newPost->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // passo il model e il singolo $post come argomento del metodo show (dependancy injection)
    public function show(Post $post)
    {
        //restituisco la view 
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // passo il model e il singolo $post come argomento del metodo show (dependancy injection)
    public function edit(Post $post)
    {
        // restituisco la view di modifica del post e il singolo post (da modificare)
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // oltre a passare (di default) i dati che arrivano dal form ($request) passo il model e il singolo $post come argomento del metodo update (dependancy injection)
    public function update(Request $request, Post $post)
    {
        // valido i dati che arrivano dal form dell'edit
        $request->validate([
            // passo al metodo validate() un array associativo in cui la chiave sarà il dato che devo controllare e come valore le caratteristiche che quel dato deve avere per poter "passare" la validazione (vedi doc: validation)
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:65535',
            'published' => 'sometimes|accepted',
        ]);

        // prendo i dati dalla request
        $data = $request->all();

        // gestisco lo slug nel caso cambiasse il titolo
        // SE il titolo del post è diverso da quello che mi arriva dalla request...
        if ($post->title != $data['title']) {
            // ...imposto il nuovo slug partendo dal nuovo titolo
            $post->slug = $this->getSlug($data['title']);
        }

        // faccio il fill() della $request
        $post->fill($data);

        // devo settare la checkbox in modo che restituisca un valore booleano (di default la checkbox restituisce "on" se è checkata e lo devo trasformare in "true")
        // il metodo isset() restituisce true o false. In questo caso "se esiste" restituisce true, altrimenti false
        $post->published = isset($data['published']);

        // salvo le modifiche al post a db
        $post->save();

        // salvo le modifiche al post a db passandogli quello che mi arriva dal form
        // $post->update($data);
        // in questo caso specifico non posso usare il metodo update perchè mi va in conflitto con la logica che gestisce lo slug

        // reindirizzo alla rotta che mi restituisce la view del post appena modificato passandolgi l'id del post
        return redirect()->route('admin.posts.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // passo il model e il singolo $post come argomento del metodo update (dependancy injection)
    public function destroy(Post $post)
    {
        // cancello il post selezionato
        $post->delete();

        // reindirizzo all'index aggiornato
        return redirect()->route('admin.posts.index');
    }

    // creo un metodo privato (passandogli $title) che mi restituisce lo slug visto che la stessa logica la utilizzerò nell'update
    private function getSlug($title) {

        // non avendolo previsto nel form, ma dovendolo avere come dato in tabella, devo generare qui uno slug univoco partendo dal title (ce lo genera laravel da una stringa)
        $slug = Str::of($title)->slug('-');

        // imposto un contatore per il controllo sullo slug
        $count = 1;

        // controllo sull'unicità dello slug 
        // FINTANTO CHE all'interno della tabella posts(Post::) trovi (first()) uno slug ('slug') uguale a questa stringa ($slug)...
        while (Post::where('slug', $slug)->first()) {
            // ...assegno a $slug il valore di $slug concatenato (. "{}") ad un trattino ed un numero ($count)...
            $slug = Str::of($title)->slug('-') . "-{$count}";
            // ...incremento $count di 1.
            $count++;
        }

        // restituisco lo slug
        return $slug;

    }
}
