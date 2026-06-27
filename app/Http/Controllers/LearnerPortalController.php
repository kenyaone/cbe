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

        // Check if this is a simplified subject (has "Lessons" strand)
        $isSimplified = $subject->strands()->where('name', 'Lessons')->exists();

        if ($isSimplified) {
            // Skip topics, go directly to lessons
            return $this->subjectLessons($gradeLevel, $subjectId);
        }

        $topics = Strand::where('learning_area_id', $subject->id)
            ->where('name', '!=', 'PDF Resources') // Hide PDF section from topics
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

    public function subjectLessons($gradeLevel, $subjectId)
    {
        $cbeCurriculum = CurriculumType::where('name', 'CBE')->first();

        $subject = LearningArea::where('curriculum_type_id', $cbeCurriculum->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        // For simplified grades, get SubStrands directly from subject's Strands
        // (skipping the topic/strand layer for simplified curricula)
        $lessons = SubStrand::whereHas('strand', function($q) use ($subject) {
            $q->where('learning_area_id', $subject->id)
              ->where('name', 'Lessons'); // Simplified grades have a "Lessons" strand
        })->orderBy('order')->get();

        return view('learner.lessons', compact('gradeLevel', 'subject', 'lessons'));
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

    public function simplifiedLessonContent($gradeLevel, $subjectId, $lessonId)
    {
        $cbeCurriculum = CurriculumType::where('name', 'CBE')->first();

        $subject = LearningArea::where('curriculum_type_id', $cbeCurriculum->id)
            ->where('grade_level', $gradeLevel)
            ->findOrFail($subjectId);

        $lesson = SubStrand::whereHas('strand', function($q) use ($subject) {
            $q->where('learning_area_id', $subject->id)
              ->where('name', 'Lessons');
        })->findOrFail($lessonId);

        $contentFiles = $lesson->contentFiles()->get();

        return view('learner.content-simple', compact('gradeLevel', 'subject', 'lesson', 'contentFiles'));
    }
}
