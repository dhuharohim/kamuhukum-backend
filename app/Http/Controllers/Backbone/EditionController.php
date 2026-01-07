<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\StorageService;

class EditionController extends Controller
{
    private $editionFor;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $role = $user->role_name;
            $this->editionFor = in_array($role, ['admin_law', 'editor_law']) ? 'law' : 'economic';
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $editions = Edition::where('edition_for', $this->editionFor)->with('articles')->get();
        return view('Contents.edition.list')->with(['editions' => $editions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = Auth::user()->role_name;
        if (in_array($role, ['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        return view("Contents.edition.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Auth::user()->role_name;
        if (in_array($role, ['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        $request->validate([
            'status' => 'required',
            'volume' => 'required',
            'issue' => [
                'required',
                Rule::unique('editions')->where(function ($query) use ($request) {
                    return $query->where('volume', $request->volume)
                        ->where('year', $request->year)
                        ->whereNull('deleted_at');
                }),
            ],
            'year' => 'required',
            'name_edition' => 'required',
            'slug' => 'nullable',
            'description' => 'nullable',
            'cover_img' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240',
                Rule::requiredIf(function () use ($request) {
                    return $request->status !== 'Draft';
                }),
            ]
        ]);

        $slug = $request->slug;
        if (empty($slug)) {
            $slug = Str::slug('vol-' . $request->volume . '-no-' . $request->issue . '-' . $request->name_edition . '-' . $this->editionFor);
        }

        $publishedDate = null;
        $coverPath = null;
        $pdfPath = null;
        if ($request->status !== 'Draft') {
            $publishedDate = date('Y-m-d H:i:s');
            $storage = new StorageService();
            $filename = 'sampul-' . $slug . '.' . $request->file('cover_img')->getClientOriginalExtension();
            $coverPath = $storage->upload($request->file('cover_img'), 'uploads/editions/' . $this->editionFor, $filename);

            if ($request->hasFile('pdf_file')) {
                $filePdfName = 'file-' . $slug . '.' . $request->file('pdf_file')->getClientOriginalExtension();
                $pdfPath = $storage->upload($request->file('pdf_file'), 'uploads/editions/' . $this->editionFor, $filePdfName);
            }
        }

        DB::beginTransaction();
        try {
            Edition::create([
                'name_edition' => $request->name_edition,
                'slug' => $slug,
                'volume' => $request->volume,
                'issue' => $request->issue,
                'year' => $request->year,
                'description' => $request->description,
                'publish_date' => $publishedDate,
                'edition_for' => $this->editionFor,
                'status' => $request->status,
                'img_path' => $coverPath,
                'pdf_path' => $pdfPath
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error saving edition: ' . $e->getMessage());
        }

        return redirect()->route('editions.index')->with('message', 'Edition created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $edition = Edition::where('id', $id)
            ->where('edition_for', $this->editionFor)
            ->with('announcement')
            ->first();

        if (empty($edition)) {
            return redirect()->route('editions.index')->with('message', 'Edition not found');
        }

        return view('Contents.edition.show')->with(['edition' => $edition]);
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
        $role = Auth::user()->role_name;
        if (in_array($role, ['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        $edition = Edition::findOrFail($id);

        $request->validate([
            'status' => 'required',
            'volume' => 'required',
            'issue' => [
                'required',
                Rule::unique('editions')
                    ->where(function ($query) use ($request) {
                        return $query->where('volume', $request->volume)
                            ->where('edition_for', $this->editionFor)
                            ->where('year', $request->year)
                            ->whereNull('deleted_at');
                    })
                    ->ignore($edition->id, 'id'),
            ],
            'year' => 'required',
            'name_edition' => 'required',
            'slug' => 'nullable',
            'description' => 'nullable',
            'cover_img' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240',
                Rule::requiredIf(function () use ($request) {
                    return $request->status !== 'Draft';
                }),
            ],
        ]);

        $slug = $request->slug;
        if (empty($request->slug)) {
            $slug = Str::slug('vol-' . $request->volume . '-no-' . $request->issue . '-' . $request->name_edition . '-' . $this->editionFor);
        }

        $publishedDate = null;
        $coverPath = $edition->img_path ?? '';
        $pdfPath = $edition->pdf_path ?? '';
        if ($request->status == 'Published') {
            $publishedDate = date('Y-m-d H:i:s');
            $storage = new StorageService();
            if ($request->hasFile('cover_img')) {
                if (!empty($coverPath) && $storage->exists($coverPath)) {
                    $storage->delete($coverPath);
                }

                $filename = 'sampul-' . $slug . '.' . $request->file('cover_img')->getClientOriginalExtension();
                $coverPath = $storage->upload($request->file('cover_img'), 'uploads/editions/' . $this->editionFor, $filename);
            }

            if ($request->hasFile('pdf_file')) {
                if (!empty($pdfPath) && $storage->exists($pdfPath)) {
                    $storage->delete($pdfPath);
                }

                $filePdfName = 'file-' . $slug . '.' . $request->file('pdf_file')->getClientOriginalExtension();
                $pdfPath = $storage->upload($request->file('pdf_file'), 'uploads/editions/' . $this->editionFor, $filePdfName);
            }
        }

        // Update the edition with the validated data
        DB::beginTransaction();
        try {
            $edition->update([
                'name_edition' => $request->name_edition,
                'slug' => $slug,
                'img_path' => $coverPath,
                'volume' => $request->volume,
                'issue' => $request->issue,
                'year' => $request->year,
                'description' => $request->description,
                'publish_date' => $publishedDate,
                'edition_for' => $this->editionFor,
                'status' => $request->status,
                'pdf_path' => $pdfPath,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Error updating edition: ' . $e->getMessage());
        }
        // Redirect or return response
        return redirect()->route('editions.index')->with('success', 'Edition updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Auth::user()->role_name;
        if (in_array($role, ['editor_law', 'editor_economy'])) {
            return redirect()->back()->with('message', 'Unauthorized');
        }

        DB::beginTransaction();
        try {
            $edition = Edition::findOrFail($id);
            $storage = new StorageService();
            if (!empty($edition->img_path) && $storage->exists($edition->img_path)) {
                $storage->delete($edition->img_path);
            }

            if (!empty($edition->pdf_path) && $storage->exists($edition->pdf_path)) {
                $storage->delete($edition->pdf_path);
            }

            $edition->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return validateErrorResponse('Error: ' . $e->getMessage());
        }

        return successResponse([], 'success');
    }
}
