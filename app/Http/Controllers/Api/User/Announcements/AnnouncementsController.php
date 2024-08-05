<?php

namespace App\Http\Controllers\Api\User\Announcements;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementsResource;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnnouncementsController extends Controller
{
    public function index($from)
    {
        $announcements = Announcement::with('edition')
            ->where('announcement_for', $from)
            ->paginate(10);

        $data = [
            'announcements' => $announcements,
            'page' => $announcements->currentPage(),
            'total' => $announcements->total(),
        ];
        return successResponse($data);
    }

    public function view($from, $slug)
    {
        $annoucement = Announcement::with(['edition', 'criterias'])
            ->where('announcement_for', $from)
            ->where('slug', $slug)->first();

        if (empty($annoucement)) {
            return recordNotFoundResponse('Announcement not found');
        }

        return successResponse($annoucement);
    }
}
