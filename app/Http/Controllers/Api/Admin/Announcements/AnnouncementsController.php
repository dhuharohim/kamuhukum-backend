<?php

namespace App\Http\Controllers\Api\Admin\Announcements;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementsResource;
use App\Models\Announcement;
use App\Models\Edition;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AnnouncementsController extends Controller
{
    public function index() {
        $announcements = Announcement::with('edition')->paginate(15);

        $data = [
            'announcements' => AnnouncementsResource::collection($announcements),
            'page' => $announcements->currentPage(),
            'total' => $announcements->total(),        
        ];

        return successResponse($data);
    }

    public function store(Request $request) 
    {
        $rules = [ 
            'edition_slug' => 'required',
            'announcement_title' => 'required',
            'submission_deadline_date' => 'required',
            'published_date' => 'required',
        ];

        $validateData = FacadesValidator::make($request->all(), $rules);

        
        if ($validateData->fails())
            return badRequestResponse($validateData->errors()->first());
            
        if(Carbon::parse($request->submission_deadline_date)->gt(Carbon::parse($request->published_date))){
            return badRequestResponse("Submission deadline, Can't greater than published date ");
        }


        $edition = Edition::where('slug', $request->edition_slug)->first();
        if(empty($edition))
            return recordNotFoundResponse("Cant't find edition");

        Announcement::create([
            'edition_id' => $edition->id,
            'announcement_title' => $request->announcement_title." Volume ".$edition->volume." isu ".$edition->issue,
            'submission_deadline_date' => $request->submission_deadline_date,
            'published_date' => $request->published_date,
        ]);
        
        return successResponse(null, 'Success created announcements');
    }

    public function update(Request $request) 
    {
      $rules = [ 
            'edition_slug' => 'required',
            'announcement_title' => 'required',
            'submission_deadline_date' => 'required',
            'published_date' => 'required',
        ];

        $validateData = FacadesValidator::make($request->all(), $rules);

        
        if ($validateData->fails())
            return badRequestResponse($validateData->errors()->first());
            
        if(Carbon::parse($request->submission_deadline_date)->gt(Carbon::parse($request->published_date))){
            return badRequestResponse("Submission deadline, Can't greater than published date ");
        }


        $edition = Edition::where('slug', $request->edition_slug)->first();
        if(empty($edition))
            return recordNotFoundResponse("Cant't find edition");

        $announcements = Announcement::where('id', $request->announcement_id)->first();
        if(empty($edition))
            return recordNotFoundResponse("Cant't find announcements");

        $announcements->update([
            'edition_id' => $edition->id,
            'announcement_title' => $request->announcement_title." Volume ".$edition->volume." isu ".$edition->issue,
            'submission_deadline_date' => $request->submission_deadline_date,
            'published_date' => $request->published_date,
        ]);
        
        return successResponse(null, 'Success updated announcements');   
    }

    public function destroy($id) 
    {
        $announcements = Announcement::where('id', $id)->first();
        if(empty($announcements))
            return recordNotFoundResponse("Cant't find announcements");
        
        $announcements->delete();
        
        return successResponse(null, "Delete announcement successfully");

    }
}
