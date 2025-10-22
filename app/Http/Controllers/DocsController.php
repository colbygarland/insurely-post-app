<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DocsController extends Controller
{
    private $storagePath = 'documents';

    private $disk = 'private'; // or set to null to use default disk

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

        // Store the file to the private bucket
        Storage::disk($this->disk)->putFileAs($this->storagePath, $request->file('document'), $doc->file_name);

        Session::flash('successMessage', 'Document uploaded successfully');

        return redirect()->route('docs');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        $filePath = $this->storagePath.'/'.$document->file_name;

        if (! Storage::disk($this->disk)->exists($filePath)) {
            Session::flash('errorMessage', 'File not found');

            return redirect()->route('docs');
        }

        return Storage::disk($this->disk)->download($filePath, $document->name.'.'.pathinfo($document->file_name, PATHINFO_EXTENSION));
    }

    public function delete($id)
    {
        $document = Document::findOrFail($id);

        // Delete the file from storage
        $filePath = $this->storagePath.'/'.$document->file_name;

        try {
            if (Storage::disk($this->disk)->exists($filePath)) {
                Storage::disk($this->disk)->delete($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting file: '.$e->getMessage());
        }

        // Delete the database record
        $document->delete();

        Session::flash('successMessage', 'Document deleted successfully');

        return redirect()->route('docs');
    }
}
