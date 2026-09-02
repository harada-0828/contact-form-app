<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('contact.index', compact('categories'));
    }

    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();
    
        $category = Category::find($validated['category_id']);

        return view('contact.confirm', compact('validated', 'category'));
    }

    public function store(StoreContactRequest $request)
    {
        if ($request->has('back')) {
            return redirect()->route('contact.index')->withInput();
        }

        Contact::create([
            'category_id' => $request->category_id,
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'gender'      => $request->gender,
            'email'       => $request->email,
            'tel'         => $request->tel,
            'address'     => $request->address,
            'building'    => $request->building,
            'detail'      => $request->detail,
        ]);

        return redirect()->route('contact.thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}