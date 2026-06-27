<?php

namespace App\Http\Controllers;

use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\SubStrand;
use App\Models\ContentFile;

class LearnerPortalController extends Controller
{
    private function getCBE()
    {
        return CurriculumType::where('name', 'CBE')->first();
    }

    public function dashboard()
    {
        $gradeLevels = LearningArea::where('curriculum_type_id', $this->getCBE()->id)
            ->select('grade_level')
            ->distinct()
            ->orderByRaw("CASE
                WHEN grade_level = 'PP1' THEN 1
                WHEN grade_level = 'PP2' THEN 2
                WHEN grade_level = 'Grade One' THEN 3
                WHEN grade_level = 'Grade Two' THEN 4
                WHEN grade_level = 'Grade Three' THEN 5
                WHEN grade_level = 'Grade Four' THEN 6
                WHEN grade_level = 'Grade Five' THEN 7
                WHEN grade_level = 'Grade Six' THEN 8
                WHEN grade_level = 'Grade Seven' THEN 9
                WHEN grade_level = 'Grade Eight' THEN 10
                WHEN grade_level = 'Grade Nine' THEN 11
                WHEN grade_level = 'Grade Ten' THEN 12
                WHEN grade_level = 'Form Three' THEN 13
                WHEN grade_level = 'Form Four' THEN 14
                ELSE 100
            END")
            ->pluck('grade_level')
            ->toArray();

        return view('learner.dashboard', compact('gradeLevels'));
    }

    public function gradeSubjects($gradeLevel)
    {
        $subjects = LearningArea::where('curriculum_type_id', $this->getCBE()->id)
            ->where('grade_level', $gradeLevel)
            ->orderBy('order')
            ->get();

        return view('learner.subjects', compact('gradeLevel', 'subjects'));
    }

    // Subject clicked — show all content grouped by type (Videos | PDFs | Interactive)
    public function subjectTopics($gradeLevel, $subjectId)
    {
        return $this->subjectContent($gradeLevel, $subjectId);
    }

    public function subjectLessons($gradeLevel, $subjectId)
    {
        return $this->subjectContent($gradeLevel, $subjectId);
    }

    private function subjectContent($gradeLevel, $subjectId)
    {
        $subject = LearningArea::where('curriculum_type_id', $this->getCBE()->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        // Content linked directly to LearningArea (PDFs, etc.)
        $directFiles = ContentFile::where('contentable_type', 'App\\Models\\LearningArea')
            ->where('contentable_id', $subject->id)
            ->get();

        // Content linked via sub-strands (videos, interactives, etc.)
        $subStrandIds = SubStrand::whereHas('strand', function ($q) use ($subject) {
            $q->where('learning_area_id', $subject->id);
        })->pluck('id');

        $subStrandFiles = ContentFile::where('contentable_type', 'App\\Models\\SubStrand')
            ->whereIn('contentable_id', $subStrandIds)
            ->get();

        $all = $directFiles->merge($subStrandFiles);

        // content_type_id: 1=PDF, 2=Video, 3=Text, 4=HTML, 5=Interactive
        $videos  = $all->whereIn('content_type_id', [2])->sortBy('order')->values();
        $pdfs    = $all->whereIn('content_type_id', [1, 3])->sortBy('order')->values();
        $htmls   = $all->whereIn('content_type_id', [4, 5])->sortBy('order')->values();

        return view('learner.subject-content', compact('gradeLevel', 'subject', 'videos', 'pdfs', 'htmls'));
    }

    // Legacy routes — redirect to subject content
    public function topicLessons($gradeLevel, $subjectId, $topicId)
    {
        return redirect()->route('learner.subject', ['gradeLevel' => $gradeLevel, 'subjectId' => $subjectId]);
    }

    public function lessonContent($gradeLevel, $subjectId, $topicId, $lessonId)
    {
        return redirect()->route('learner.subject', ['gradeLevel' => $gradeLevel, 'subjectId' => $subjectId]);
    }

    public function simplifiedLessonContent($gradeLevel, $subjectId, $lessonId)
    {
        return redirect()->route('learner.subject', ['gradeLevel' => $gradeLevel, 'subjectId' => $subjectId]);
    }
}
