<?php

namespace App\Http\Controllers\Api\Admin\Journals;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JournalController extends Controller
{
    public function index() {
        $journals =  Journal::all();

        successResponse($journals);
    }

    public function store(Request $request) {
        $user = Auth::user();
                
        $rules = [ "file" => "required"];

        $validateData = Validator::make($request->all(), $rules, [
        'file.required' => 'You have to choose the file!',
        ]);  

        if($validateData->fails()){
            return badRequestResponse($validateData->errors());
        }
        

        if($request->file('file')) 
        {
            $parser = new \Smalot\PdfParser\Parser(); 
            $file = $request->file('file');
            $file = $file; 
            $time = time();
            $filename = $time . '.' . $request->file('file')->extension();
            $filePath = public_path() . '/files/uploads/pdf/'.$time.'/'.$filename;
            $file->move($filePath, $filename);
            
            // Parse pdf file using Parser library 
            $pdf = $parser->parseFile($filePath."/".$filename); 
            
            // Extract text from PDF 
            $textContent = $pdf->getText();
            $textContent = str_replace("\t"," ",$textContent);
            $textContent = str_replace("\n","",$textContent);
    
            $abstract[0] = $this->string_between_two_string($textContent, 'Abstract: ', 'Keywords');
            $abstract[1] = $this->string_between_two_string($textContent, 'Abstrak: ', 'Kata kunci');
        }

        $slug = str_replace(" ","-",$request->name);

        $journals = new Journal();
        $journals->name = $request->name;
        $journals->slug = $slug;
        $journals->description = $request->description;
        $journals->upload_by = $user->name;
        $journals->abstract = $abstract[0];
        $journals->abstrak = $abstract[1];
        $journals->category_id = $request->category;
        $journals->view = 0;
        $journals->pdf_path = '/files/uploads/pdf/'.$time.'/'.$filename;
        $journals->save();

        return successResponse($journals);
    }

    function string_between_two_string($str, $starting_word, $ending_word)
    {
        $subtring_start = strpos($str, $starting_word);
        //Adding the starting index of the starting word to
        //its length would give its ending index
        $subtring_start += strlen($starting_word); 
        //Length of our required sub string
        $size = strpos($str, $ending_word, $subtring_start) - $subtring_start; 
        // Return the substring from the index substring_start of length size
        return substr($str, $subtring_start, $size); 
    }
}
