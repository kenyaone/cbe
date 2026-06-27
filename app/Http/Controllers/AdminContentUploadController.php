<?php

namespace App\Http\Controllers;

use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use Illuminate\Http\Request;

class AdminContentUploadController extends Controller
{
    public function create()
    {
        $grades = LearningArea::select('grade_level')->distinct()
            ->orderByRaw("CASE
                WHEN grade_level = 'PP1' THEN 1
                WHEN grade_level = 'PP2' THEN 2
                WHEN grade_level LIKE 'Grade%' THEN CAST(SUBSTR(grade_level, 7) AS INTEGER) + 2
                ELSE 100
            END")
            ->pluck('grade_level');

        return view('admin.content.upload', compact('grades'));
    }

    public function getSubjects($grade)
    {
        $subjects = LearningArea::where('grade_level', $grade)
            ->orderBy('order')
            ->get(['id', 'name']);

        return response()->json($subjects);
    }

    public function getStrands($subjectId)
    {
        $strands = Strand::where('learning_area_id', $subjectId)
            ->orderBy('order')
            ->get(['id', 'name']);

        return response()->json($strands);
    }

    public function getSubStrands($strandId)
    {
        $subStrands = SubStrand::where('strand_id', $strandId)
            ->orderBy('order')
            ->get(['id', 'name']);

        return response()->json($subStrands);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'required|string',
            'subject' => 'required|integer|exists:learning_areas,id',
            'strand' => 'required|integer|exists:strands,id',
            'sub_strand' => 'required|integer|exists:sub_strands,id',
            'file' => 'required|file|max:1048576', // 1GB max
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        // Determine content type
        $contentType = $this->getContentType($extension);
        if (!$contentType) {
            return back()->withErrors(['file' => 'Unsupported file type. Allowed: mp4, pdf, html']);
        }

        // Create storage directory
        $storagePath = storage_path('app/media/uploads/' . auth()->user()->id);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Store file
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move($storagePath, $filename);
        $filePath = $storagePath . '/' . $filename;

        // Get or create content type
        $type = ContentType::firstOrCreate(['name' => $contentType]);

        // Create content file record
        ContentFile::create([
            'title' => $validated['title'],
            'file_path' => $filePath,
            'content_type_id' => $type->id,
            'contentable_id' => $validated['sub_strand'],
            'contentable_type' => SubStrand::class,
        ]);

        return redirect()->route('admin.content')->with('success', 'Content uploaded successfully!');
    }

    private function getContentType($extension)
    {
        $extension = strtolower($extension);

        return match($extension) {
            'mp4', 'avi', 'mov', 'mkv', 'flv', 'wmv' => 'Video',
            'pdf' => 'PDF',
            'html', 'htm' => 'Interactive',
            default => null,
        };
    }
}
