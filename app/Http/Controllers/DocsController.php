<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DocsController extends Controller
{
    public function index()
    {
        $documents = Document::all();

        return view('docs', ['documents' => $documents]);
    }

    public function create(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:'.implode(',', Document::$TYPE),
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
        ]);

        $doc = new Document;
        $doc->name = $request->name;

        $timestamp = now()->format('Y-m-d_H-i-s');
        $extension = $request->file('document')->getClientOriginalExtension();
        $originalName = pathinfo($request->file('document')->getClientOriginalName(), PATHINFO_FILENAME);
        $doc->file_name = $originalName.'_'.$timestamp.'.'.$extension;
        $doc->type = $request->type;
        $doc->updatedBy = Auth::user()->id;

        $doc->save();

        Session::flash('successMessage', 'Document uploaded successfully');

        return redirect()->route('docs');
    }
}
