<?php

namespace App\Http\Controllers\Api\User\Announcements;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementsResource;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnnouncementsController extends Controller
{
    public function index() {
        $announcements = Announcement::with('edition')->whereDate('published_date', ">", Carbon::now()->format('Y-m-d'))->paginate(10);

        $data = [
            'announcements' => AnnouncementsResource::collection($announcements),
            'page' => $announcements->currentPage(),
            'total' => $announcements->total(),            
        ];
        return successResponse($data);
    }

    public function view($slug)
    {
        $annoucement = Announcement::with(['edition', 'criteria'])->where('slug', $slug)->first();

        if(empty($annoucement))
            return recordNotFoundResponse('Announcement not found');
        
        return successResponse(new AnnouncementsResource($annoucement));
    }
}
