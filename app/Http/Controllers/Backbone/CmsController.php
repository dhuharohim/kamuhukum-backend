<?php

namespace App\Http\Controllers\Backbone;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionContent;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    private $userFor;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->userFor = $user->hasRole(['admin_law']) ? 'law' : 'economy';
            return $next($request);
        });
    }

    public function index()
    {
        $sections = Section::where('section_for', $this->userFor)->with('contents')->orderBy('position', 'asc')->get();
        return view('Contents.cms.index', compact('sections'));
    }

    public function create()
    {
        return view('Contents.cms.manage');
    }

    public function show($slug)
    {
        $section = Section::where('slug', $slug)->where('section_for', $this->userFor)->with(['contents', 'media'])->first();
        if (!$section) {
            return redirect()->route('cms.index')->with('error', 'Section not found');
        }

        return view('Contents.cms.manage', compact('section'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'preview' => 'required|image|mimes:jpeg,png,jpg',
            'category' => 'required|in:main,header,footer',
            'key.*' => 'required',
            'valueText.*' => 'required'
        ]);

        DB::beginTransaction();
        try {
            if (!$request->hasFile('preview')) {
                throw new Exception('You must provide a preview file');
            }

            $slug = Str::slug($request->name);
            $filename = 'preview-' . $slug . '.' . $request->file('preview')->getClientOriginalExtension();
            $previewPath = $request->file('preview')->storeAs('uploads/cms/sections/' . $this->userFor, $filename);

            $section = Section::create([
                'name' => $request->name,
                'slug' => $slug,
                'position' => $request->category,
                'preview' => $previewPath,
                'section_for' => $this->userFor,
                'is_active' => 0,
            ]);

            foreach ($request->key as $idx => $key) {
                $text = $request->valueText[$idx];

                SectionContent::create([
                    'section_id' => $section->id,
                    'key' => $key,
                    'value' => $text,
                    'type' => 'text'
                ]);
            }

            DB::commit();
            return redirect()->route('cms.index')->with('success', 'Content has been added successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage() . ': ' . $e->getLine()]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'preview' => 'nullable|image|mimes:jpeg,png,jpg',
            'category' => 'required|in:main,header,footer',
            'key.*' => 'required',
            'valueText.*' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $section = Section::where('id', $id)->where('section_for', $this->userFor)->first();
            if (!$section) {
                throw new Exception('Section not found');
            }

            $slug = Str::slug($request->name);

            // Handle preview image update if provided
            if ($request->hasFile('preview')) {
                // Delete old preview image
                if (Storage::exists($section->preview)) {
                    Storage::delete($section->preview);
                }

                $filename = 'preview-' . $slug . '.' . $request->file('preview')->getClientOriginalExtension();
                $previewPath = $request->file('preview')->storeAs('uploads/cms/sections/' . $this->userFor, $filename);
                $section->preview = $previewPath;
            }

            // Update section details
            $section->name = $request->name;
            $section->slug = $slug;
            $section->position = $request->category;
            $section->save();

            // Get existing contents for cleanup
            $existingContents = SectionContent::where('section_id', $section->id)->get();

            // Delete existing content
            foreach ($existingContents as $content) {
                $content->delete();
            }

            // Create new content and track images
            foreach ($request->key as $idx => $key) {
                $text = $request->valueText[$idx];

                // Clean up unused images from this content block
                if (isset($existingContents[$idx])) {
                    $this->cleanupUnusedImages($text, $existingContents[$idx]->value);
                }

                SectionContent::create([
                    'section_id' => $section->id,
                    'key' => $key,
                    'value' => $text,
                    'type' => 'text'
                ]);
            }

            DB::commit();
            return redirect()->route('cms.index')->with('success', 'Content has been updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage() . ': ' . $e->getLine()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $section = Section::where('id', $id)->where('section_for', $this->userFor)->first();
            if (!$section) {
                throw new Exception('Section not found');
            }

            // Delete preview image if exists
            if (Storage::exists($section->preview)) {
                Storage::delete($section->preview);
            }

            // Get and delete section contents
            $sectionContents = SectionContent::where('section_id', $id)->get();
            foreach ($sectionContents as $content) {
                // Clean up any images in the content
                if ($content->type == 'text') {
                    $this->cleanupUnusedImages($content->value, null);
                } elseif ($content->type == 'image' && Storage::exists($content->value)) {
                    Storage::delete($content->value);
                }
                $content->delete();
            }

            $section->delete();
            DB::commit();
            return response()->json(['message' => 'Content has been deleted successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage() . ': ' . $e->getLine()], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // 2MB max
            ]);

            if (!$request->hasFile('image')) {
                throw new Exception('No image file provided');
            }

            $file = $request->file('image');
            $filename = 'editor-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/cms/editor/' . $this->userFor, $filename, 'public');

            if (!$path) {
                throw new Exception('Failed to store image');
            }

            return response()->json([
                'url' => config('app.url') . 'admin/storage/' . $path,
                'success' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Clean up unused images from editor content
     * @param string $content HTML content from editor
     * @return array Array of image paths that are still in use
     */
    private function getUsedImagesFromContent($content)
    {
        $usedImages = [];

        // Extract all image sources from the content
        preg_match_all('/<img[^>]+src="([^">]+)"/', $content, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $src) {
                // Convert URL to storage path by removing the app URL and /storage prefix
                $path = str_replace(config('app.url'), '', $src);
                if (Storage::exists('public/' . $path)) {
                    $usedImages[] = 'public/' . $path;
                }
            }
        }

        return $usedImages;
    }

    /**
     * Clean up unused images
     * @param string $newContent New content from editor
     * @param string|null $oldContent Old content from database
     */
    private function cleanupUnusedImages($newContent, $oldContent = null)
    {
        // Get images from old and new content
        $newImages = $this->getUsedImagesFromContent($newContent);
        $oldImages = $oldContent ? $this->getUsedImagesFromContent($oldContent) : [];

        // Find images that were in old content but not in new content
        $unusedImages = array_diff($oldImages, $newImages);

        // Delete unused images
        foreach ($unusedImages as $image) {
            if (Storage::exists($image)) {
                Storage::delete($image);
            }
        }
    }
}
