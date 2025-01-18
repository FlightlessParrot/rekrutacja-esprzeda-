<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetRequest;
use App\Models\Category;
use App\Models\Tag;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Enums\Status;
use App\Http\Requests\PhotoUploadRequest;
use App\Http\Requests\UpdatePetRequest;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Status $status)
    {
        $response = Http::get('https://petstore.swagger.io/v2/pet/findByStatus', ['status'=>$status->value]);
        if($response->status() !== 200){
            return back()->with(['message'=>'Something went wrong','severity'=>'error'], 500);
        }
        $pets = $response->json();
        $pets = collect($pets);

        $pets->map(function($pet){
            $pet['category'] = isset($pet['category']['name']) ?
             Category::firstOrCreate(['name'=>$pet['category']['name']]): 
             null;
            $pet['tags'] = collect($pet['tags']?? [])->map(function($tag){
                return Tag::firstOrCreate(['name'=>$tag['name']]);
            });
            return $pet;
        });
        return Inertia::render('Pet/IndexPets', ['pets' => $pets]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Pet/AddPet', [
            'categories' => Category::all(),
            'tags' => Tag::all(),
            'statuses'=> Status::cases()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePetRequest $request) : RedirectResponse
    {
      
        $category=Category::findOrFail($request->category['id']);
        $tags=new Collection();
        
        foreach($request->tags as $tag){
            $tags->push(Tag::findOrFail($tag['id']));
        }
       
        $tags = $tags->map(function($tag){
            return ['name'=>$tag->name,'id'=>$tag->id];
        });
        
        $data = [
            'name' => $request->name,
            'category' => ['name'=>$category->name,'id'=>$category->id],
            'tags' => $tags,
            "photoUrls" => [],
            "status" => "available"
        ];
        
        $response = Http::post('https://petstore.swagger.io/v2/pet', $data);
        
        if($response->status() === 200){
            return to_route('pets.edit',$response->json()['id'])->with(['message'=>'Pet created successfully','severity'=>'success'], 200);
        }else{          
            return back()->with(['message'=>'Pet creation failed','severity'=>'error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {        
        
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $response = Http::get('https://petstore.swagger.io/v2/pet/'.$id);
       
        if($response->status() !== 200){
            return back()->with(['message'=>'Pet not found','severity'=>'error'], 404);
        }
        $pet = $response->json();
        $pet['category'] = isset($pet['category']['name']) ?
             Category::firstOrCreate(['name'=>$pet['category']['name']]): 
             null;
        $pet['tags'] = collect($pet['tags']?? [] )->map(function($tag){
            return Tag::firstOrCreate(['name'=>$tag['name']]);
        });
        return Inertia::render('Pet/EditPet', ['pet' => $pet, 'categories' => Category::all(), 'tags' => Tag::all()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePetRequest $request, string $id)
    {
        $response = Http::get('https://petstore.swagger.io/v2/pet/'.$id);
        if($response->status() !== 200){
            return back()->with(['message'=>'Something went wrong','severity'=>'error'], 404);
        }
        $pet=$response->json();
        $data = $request->all();
        $category=Category::findOrFail($data['category']['id']);
        $tags=new Collection();
        foreach($data['tags'] as $tag){
            $tags->push(Tag::findOrFail($tag['id']));
        };
        $tags = $tags->map(function($tag){
            return ['name'=>$tag->name,'id'=>$tag->id];
        });
        $data = [
            'name' => $data['name'],
            'category' => ['name'=>$category->name,'id'=>$category->id],
            'tags' => $tags->toArray(),
            "photoUrls" => $pet['photoUrls'],
            "status" => "available"
        ];
        $response = Http::put('https://petstore.swagger.io/v2/pet', $data);
        if($response->status() === 200){
            return back()->with(['message'=>'Pet updated successfully','severity'=>'success'], 200);
        }else{  
            return back()->with(['message'=>'We couldn\'t update the pet','severity'=>'error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = Http::delete('https://petstore.swagger.io/v2/pet/'.$id);
        if($response->status() === 200){
            return to_route('dashboard')->with(['message'=>'Pet deleted successfully','severity'=>'success'], 200);
        }else{  
            return back()->with(['message'=>'We couldn\'t delete the pet','severity'=>'error'], 500);
        }
    }

    public function fileUpload(PhotoUploadRequest $request,string $id)
    {
     
        $response = Http::asMultipart()->post('https://petstore.swagger.io/v2/pet/'.$id.'/uploadImage', [
            'file' => $request->file('image')
        ]);
        if($response->status() === 200){
            return back()->with(['message'=>'Image uploaded successfully','severity'=>'success'], 200);
        }else{  
            return back()->with(['message'=>'We couldn\'t upload the image','severity'=>'error'], 500);
        }
    }
}
