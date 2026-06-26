<?php

namespace App\Http\Controllers;

use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class LearnerPortalController extends Controller
{
    public function dashboard()
    {
        // Get all grade levels from CBE curriculum
        $cbeCurriculum = CurriculumType::where('name', 'CBE')->first();

        $gradeLevels = LearningArea::where('curriculum_type_id', $cbeCurriculum->id)
            ->select('grade_level')
            ->distinct()
            ->orderByRaw("CASE
                WHEN grade_level = 'PP1' THEN 1
                WHEN grade_level = 'PP2' THEN 2
                WHEN grade_level LIKE 'Grade%' THEN CAST(SUBSTR(grade_level, 7) AS INTEGER) + 2
                ELSE 100
            END")
            ->pluck('grade_level')
            ->toArray();

        return view('learner.dashboard', compact('gradeLevels'));
    }

    public function gradeSubjects($gradeLevel)
    {
        // Get all subjects for a specific grade level
        $cbeCurriculum = CurriculumType::where('name', 'CBE')->first();

        $subjects = LearningArea::where('curriculum_type_id', $cbeCurriculum->id)
            ->where('grade_level', $gradeLevel)
            ->orderBy('order')
            ->get();

        return view('learner.subjects', compact('gradeLevel', 'subjects'));
    }

    public function subjectTopics($gradeLevel, $subjectId)
    {
        $cbeCurriculum = CurriculumType::where('name', 'CBE')->first();

        $subject = LearningArea::where('curriculum_type_id', $cbeCurriculum->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        $topics = Strand::where('learning_area_id', $subject->id)
            ->orderBy('order')
            ->get();

        return view('learner.topics', compact('gradeLevel', 'subject', 'topics'));
    }

    public function topicLessons($gradeLevel, $subjectId, $topicId)
    {
        $cbeCurriculum = CurriculumType::where('name', 'CBE')->first();

        $subject = LearningArea::where('curriculum_type_id', $cbeCurriculum->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        $topic = Strand::where('learning_area_id', $subject->id)
            ->findOrFail($topicId);

        $lessons = SubStrand::where('strand_id', $topic->id)
            ->orderBy('order')
            ->get();

        return view('learner.lessons', compact('gradeLevel', 'subject', 'topic', 'lessons'));
    }

    public function lessonContent($gradeLevel, $subjectId, $topicId, $lessonId)
    {
        $cbeCurriculum = CurriculumType::where('name', 'CBE')->first();

        $subject = LearningArea::where('curriculum_type_id', $cbeCurriculum->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        $topic = Strand::where('learning_area_id', $subject->id)
            ->findOrFail($topicId);

        $lesson = SubStrand::where('strand_id', $topic->id)
            ->findOrFail($lessonId);

        $contentFiles = $lesson->contentFiles()->get();

        return view('learner.content', compact('gradeLevel', 'subject', 'topic', 'lesson', 'contentFiles'));
    }
}
