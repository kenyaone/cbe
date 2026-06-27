<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand as SubStrandModel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SimplifyAllGradesSeeder extends Seeder
{
    private $gradeStructures = [
        'PP1' => ['Mathematical Activities', 'Language Activities', 'Creative Activities', 'Environmental Activities', 'CRE', 'HRE'],
        'PP2' => ['Mathematical Activities', 'Language Activities', 'Creative Activities', 'Environmental Activities', 'CRE', 'HRE'],
        'Grade One' => ['Mathematics', 'English', 'Kiswahili', 'Environmental Studies', 'Creative Arts', 'CRE', 'HRE'],
        'Grade Two' => ['Mathematics', 'English', 'Kiswahili', 'Environmental Studies', 'Creative Arts', 'CRE', 'HRE'],
        'Grade Three' => ['Mathematics', 'English', 'Kiswahili', 'Science', 'Social Studies', 'Creative Arts', 'CRE', 'HRE'],
        'Grade Four' => ['Mathematics', 'English', 'Kiswahili', 'Science', 'Social Studies', 'Creative Arts', 'CRE', 'HRE'],
        'Grade Five' => ['Mathematics', 'English', 'Kiswahili', 'Science', 'Social Studies', 'Creative Arts', 'CRE', 'HRE'],
        'Grade Six' => ['Mathematics', 'English', 'Kiswahili', 'Science', 'Social Studies', 'Creative Arts', 'CRE', 'HRE'],
        'Grade Seven' => ['Mathematics', 'Integrated Science', 'English', 'Kiswahili', 'Social Studies', 'Agriculture', 'Creative Arts and Sports', 'CRE', 'IRE', 'Pre-Technical Studies'],
        'Grade Eight' => ['Mathematics', 'Integrated Science', 'English', 'Kiswahili', 'Social Studies', 'Agriculture', 'Creative Arts and Sports', 'CRE', 'IRE', 'Pre-Technical Studies'],
        'Grade Nine' => ['Mathematics', 'Integrated Science', 'English', 'Kiswahili', 'Social Studies', 'Agriculture', 'Creative Arts and Sports', 'CRE', 'IRE', 'Pre-Technical Studies'],
        'Grade Ten' => ['Mathematics', 'Integrated Science', 'English', 'Kiswahili', 'Social Studies', 'Agriculture', 'Creative Arts and Sports', 'CRE', 'IRE', 'Pre-Technical Studies'],
    ];

    public function run()
    {
        $this->command->info('=== SIMPLIFYING ALL GRADES ===');
        $this->command->info('This will convert all grades to: Subject → Video Lessons structure');
        $this->command->info('');

        $curriculumType = CurriculumType::firstOrCreate(['name' => 'CBE']);

        foreach ($this->gradeStructures as $gradeName => $subjects) {
            $this->command->info("Processing: $gradeName");

            // Delete old curriculum structure (cascades to delete content files)
            $deletedSubjects = LearningArea::where('grade_level', $gradeName)->delete();

            if ($deletedSubjects > 0) {
                $this->command->line("  Deleted old curriculum structure");
            }

            // Create simplified structure
            $order = 0;

            foreach ($subjects as $subjectName) {
                $order++;
                // Generate unique code by concatenating grade and subject order
                $baseGrade = strtoupper(str_replace(' ', '', substr($gradeName, 0, 2)));
                $code = 'C' . $baseGrade . str_pad($order, 3, '0', STR_PAD_LEFT);

                // Ensure code is unique
                while (LearningArea::where('code', $code)->exists()) {
                    $order++;
                    $code = 'C' . $baseGrade . str_pad($order, 3, '0', STR_PAD_LEFT);
                }

                $subject = LearningArea::create([
                    'curriculum_type_id' => $curriculumType->id,
                    'grade_level' => $gradeName,
                    'name' => $subjectName,
                    'code' => $code,
                    'order' => $order,
                ]);

                // Create single "Video Lessons" strand
                Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $code . 'S01',
                    'name' => 'Video Lessons',
                    'order' => 1,
                ]);
            }

            $this->command->line("  Created $order subjects with Video Lessons strand");
        }

        // Now relink all videos
        $this->command->info('');
        $this->command->info('=== RELINKING ALL VIDEOS ===');
        $videoCount = $this->relinkAllVideos();

        $this->command->info('');
        $this->command->info("✅ COMPLETE: $videoCount videos now direct lessons");
        $this->command->info('All grades simplified successfully!');
    }

    private function relinkAllVideos()
    {
        $basePath = '/home/tele/cbe-platform/storage/app/media';
        $totalVideos = 0;

        $folderMap = [
            'PP1' => 'PP1',
            'PP2' => 'PP2',
            'Grade One' => 'Grade One Complete',
            'Grade Two' => 'Grade Two Complete',
            'Grade Three' => 'Grade Three Complete',
            'Grade Four' => 'Grade Four Complete',
            'Grade Five' => 'Grade Five Complete',
            'Grade Six' => 'Grade Six Complete',
            'Grade Seven' => 'Grade Seven Complete',
            'Grade Eight' => 'Grade Eight Complete',
            'Grade Nine' => 'grade-nine',
            'Grade Ten' => 'grade-ten',
        ];

        foreach ($folderMap as $gradeName => $folderName) {
            $gradeBasePath = $basePath . '/' . $folderName;
            if (!is_dir($gradeBasePath)) {
                continue;
            }

            $this->command->line("  Processing $gradeName videos...");

            $videoIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($gradeBasePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $gradeVideoCount = 0;
            $processedFolders = [];

            foreach ($videoIterator as $file) {
                if ($file->isDir()) continue;

                $extension = strtolower($file->getExtension());
                if (!in_array($extension, ['mp4', 'avi', 'mov', 'mkv'])) continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                // Find the subject folder
                $parts = explode('/', $filePath);
                $subjectFolder = null;
                foreach ($parts as $part) {
                    if (in_array($part, ['Math', 'Mathematics', 'English', 'Science', 'Kiswahili', 'Social', 'Creative', 'CRE', 'HRE', 'IRE', 'Agriculture', 'Pre-Technical', 'Integrated SCIENCE', 'INTEGRATED SCIENCE'])) {
                        $subjectFolder = $part;
                        break;
                    }
                }

                // Match to subject and strand
                $subject = $this->matchSubject($gradeName, $filePath);
                if (!$subject) continue;

                $strand = $subject->strands()->where('name', 'Video Lessons')->first();
                if (!$strand) continue;

                // Create subStrand for video
                $title = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $videoOrder = $strand->subStrands()->count() + 1;

                $subStrand = SubStrandModel::create([
                    'strand_id' => $strand->id,
                    'code' => $strand->code . 'V' . str_pad($videoOrder, 2, '0', STR_PAD_LEFT),
                    'name' => $title,
                    'order' => $videoOrder,
                ]);

                $contentType = ContentType::firstOrCreate(['name' => 'Video']);

                ContentFile::create([
                    'title' => $title,
                    'file_path' => $filePath,
                    'content_type_id' => $contentType->id,
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => 'App\\Models\\SubStrand',
                    'is_published' => true,
                ]);

                $gradeVideoCount++;
                $totalVideos++;
            }

            $this->command->line("    Linked $gradeVideoCount videos");
        }

        return $totalVideos;
    }

    private function matchSubject($gradeName, $filePath)
    {
        $lowerPath = strtolower($filePath);

        // Subject matching patterns
        $subjectMap = [
            'Mathematics' => ['math'],
            'English' => ['english', 'eng'],
            'Kiswahili' => ['kiswahili', 'kisw'],
            'Science' => ['science', 'sci'],
            'Integrated Science' => ['integrated science', 'science'],
            'Social Studies' => ['social', 'geography', 'history'],
            'Environmental Studies' => ['environmental', 'env'],
            'Creative Arts' => ['creative', 'art', 'music', 'dance'],
            'Creative Arts and Sports' => ['creative', 'sports'],
            'Agriculture' => ['agric', 'agriculture'],
            'Pre-Technical Studies' => ['technical', 'pre-technical'],
            'CRE' => ['cre', 'christian'],
            'HRE' => ['hre', 'hindu'],
            'IRE' => ['ire', 'hindu', 'indian'],
        ];

        foreach ($subjectMap as $subjectName => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($lowerPath, $pattern) !== false) {
                    $subject = LearningArea::where('grade_level', $gradeName)
                        ->where('name', 'like', "%$subjectName%")
                        ->first();
                    if ($subject) {
                        return $subject;
                    }
                }
            }
        }

        // Fallback: return first subject
        return LearningArea::where('grade_level', $gradeName)->first();
    }
}
