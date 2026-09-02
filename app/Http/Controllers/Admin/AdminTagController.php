<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;

class AdminTagController extends Controller
{
    public function store(StoreTagRequest $request)
    {
        Tag::create($request->validated());
        return redirect()->route('admin.index');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());
        return redirect()->route('admin.index');
    }
    
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('admin.index');
    }
}