<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Edition;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    private $annFor;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->annFor = $user->hasRole(['admin_law', 'editor_law']) ? 'law' : 'economic';
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::where('announcement_for', $this->annFor)->with('edition')->get();
        return view('Contents.announcements.list')->with('announcements', $announcements);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $editions = Edition::where('edition_for', $this->annFor)->get();
        return view('Contents.announcements.create')->with('editions', $editions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        $request->validate([
            'title' => 'required',
            'edition' => 'nullable',
        ]);

        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug($request->title . '-' . $this->annFor);
        }

        $publishedDate = null;
        if (isset($request->published) && $request->published == 'on') {
            $publishedDate = date('Y-m-d');
        }

        $editionId = null;
        if (!empty($request->edition)) {
            $edition = Edition::where('edition_for', $this->annFor)->where('id', $request->edition)->first();
            if (empty($edition)) {
                return redirect()->back()->with('message', 'Invalid edition');
            }

            $editionId = $edition->id;
        }

        DB::beginTransaction();
        try {
            Announcement::create([
                'edition_id' => $editionId,
                'announcement_for' => $this->annFor,
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'submission_deadline_date' => $request->submission_deadline ?? null,
                'published_date' => $publishedDate,
                'extend_submission_date' => $request->extend_submission_date,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Failed to create announcement: ' . $e->getMessage());
        }

        return redirect()->route('announcements.index')->with('message', 'Announcement created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $announcement = Announcement::where('id', $id)
            ->where('announcement_for', $this->annFor)
            ->with('edition')
            ->first();

        $editions = Edition::where('edition_for', $this->annFor)->get();
        if (empty($announcement)) {
            return redirect()->route('announcements.index')->with('message', 'Announcement not found');
        }

        return view('Contents.announcements.show')->with(['announcement' => $announcement, 'editions' => $editions]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        $announcement = Announcement::where('id', $id)
            ->where('announcement_for', $this->annFor)
            ->first();

        if (empty($announcement)) {
            return redirect()->back()->with('message', 'Announcement not found');
        }

        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug($request->title . '-' . $this->annFor);
        }

        $publishedDate = $announcement->published_date;
        if (isset($request->takedown) && $request->takedown == 'on') {
            $publishedDate = null;
        } else if (isset($request->published) && $request->published == 'on') {
            $publishedDate = date('Y-m-d');
        }

        $editionId = null;
        if (!empty($request->edition)) {
            $edition = Edition::where('edition_for', $this->annFor)->where('id', $request->edition)->first();
            if (empty($edition)) {
                return redirect()->back()->with('message', 'Invalid edition');
            }

            $editionId = $edition->id;
        }

        DB::beginTransaction();
        try {
            $announcement->update([
                'edition_id' => $editionId,
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'submission_deadline_date' => $request->submission_deadline ?? null,
                'published_date' => $publishedDate,
                'extend_submission_date' => $request->extend_submission_date,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Failed to update announcement: ' . $e->getMessage());
        }

        return redirect()->route('announcements.show', $id)->with('message', 'Announcement updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->hasRole(['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        $announcement = Announcement::where('id', $id)
            ->where('announcement_for', $this->annFor)
            ->first();

        if (empty($announcement)) {
            return redirect()->back()->with('message', 'Announcement not found');
        }

        DB::beginTransaction();
        try {
            $announcement->forceDelete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Failed to delete announcement: ' . $e->getMessage());
        }

        return redirect()->route('announcements.index')->with('message', 'Announcement deleted successfully');
    }
}
