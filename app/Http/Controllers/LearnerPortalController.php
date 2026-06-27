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

    // Subject clicked — always go directly to lessons list (no topics)
    public function subjectTopics($gradeLevel, $subjectId)
    {
        return $this->subjectLessons($gradeLevel, $subjectId);
    }

    public function subjectLessons($gradeLevel, $subjectId)
    {
        $subject = LearningArea::where('curriculum_type_id', $this->getCBE()->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        // Get ALL content files for this subject across all strands/sub-strands
        $lessons = SubStrand::whereHas('strand', function ($q) use ($subject) {
            $q->where('learning_area_id', $subject->id);
        })
        ->whereHas('contentFiles')
        ->with('contentFiles')
        ->orderBy('order')
        ->get();

        return view('learner.lessons', compact('gradeLevel', 'subject', 'lessons'));
    }

    // Lesson content — works for both simplified and topic-based
    public function simplifiedLessonContent($gradeLevel, $subjectId, $lessonId)
    {
        $subject = LearningArea::where('curriculum_type_id', $this->getCBE()->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        $lesson = SubStrand::whereHas('strand', function ($q) use ($subject) {
            $q->where('learning_area_id', $subject->id);
        })->findOrFail($lessonId);

        $contentFiles = $lesson->contentFiles()->get();

        return view('learner.content-simple', compact('gradeLevel', 'subject', 'lesson', 'contentFiles'));
    }

    // Legacy topic-based routes still work but redirect to simplified
    public function topicLessons($gradeLevel, $subjectId, $topicId)
    {
        return redirect()->route('learner.subject', ['gradeLevel' => $gradeLevel, 'subjectId' => $subjectId]);
    }

    public function lessonContent($gradeLevel, $subjectId, $topicId, $lessonId)
    {
        return $this->simplifiedLessonContent($gradeLevel, $subjectId, $lessonId);
    }
}
