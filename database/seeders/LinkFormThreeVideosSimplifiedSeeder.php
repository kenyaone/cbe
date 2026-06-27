<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LinkFormThreeVideosSimplifiedSeeder extends Seeder
{
    public function run()
    {
        $basePath = '/home/tele/cbe-platform/storage/app/media/form-three';

        if (!is_dir($basePath)) {
            $this->command->error("Form Three content directory not found");
            return;
        }

        $videoCount = 0;
        $pdfCount = 0;

        // Get all subjects in Form Three
        $subjects = \App\Models\LearningArea::where('grade_level', 'Form Three')->get();

        foreach ($subjects as $subject) {
            // Get the "Video Lessons" strand
            $strand = $subject->strands()->first();
            if (!$strand) continue;

            // Match subject folder
            $subjectFolder = $this->getSubjectFolder($subject->name);
            $subjectPath = $basePath . '/' . $subjectFolder;

            if (!is_dir($subjectPath)) {
                continue;
            }

            // Process videos in this subject folder
            $videoOrder = 0;
            $videoIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($subjectPath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($videoIterator as $file) {
                if ($file->isDir()) continue;

                $extension = strtolower($file->getExtension());

                // Process videos
                if (in_array($extension, ['mp4', 'avi', 'mov', 'mkv'])) {
                    $filePath = $file->getRealPath();
                    $title = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                    if (ContentFile::where('file_path', $filePath)->exists()) {
                        continue;
                    }

                    // Create sub-strand for each video (video becomes the lesson)
                    $videoOrder++;
                    $subStrand = SubStrand::create([
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
                        'contentable_type' => SubStrand::class,
                        'is_published' => true,
                    ]);

                    $videoCount++;
                }
                // Process PDFs separately (keep at subject level or in root)
                elseif ($extension === 'pdf') {
                    $filePath = $file->getRealPath();
                    $title = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                    if (ContentFile::where('file_path', $filePath)->exists()) {
                        continue;
                    }

                    // Create a single PDF sub-strand if not exists
                    $pdfStrand = $subject->strands()
                        ->where('name', 'PDF Resources')
                        ->first();

                    if (!$pdfStrand) {
                        $pdfStrand = Strand::create([
                            'learning_area_id' => $subject->id,
                            'code' => $subject->code . 'SPDF',
                            'name' => 'PDF Resources',
                            'order' => 2,
                        ]);

                        SubStrand::create([
                            'strand_id' => $pdfStrand->id,
                            'code' => $pdfStrand->code . 'SS01',
                            'name' => 'Study Materials',
                            'order' => 1,
                        ]);
                    }

                    $pdfSubStrand = $pdfStrand->subStrands()->first();

                    $contentType = ContentType::firstOrCreate(['name' => 'PDF']);

                    ContentFile::create([
                        'title' => $title,
                        'file_path' => $filePath,
                        'content_type_id' => $contentType->id,
                        'contentable_id' => $pdfSubStrand->id,
                        'contentable_type' => SubStrand::class,
                        'is_published' => true,
                    ]);

                    $pdfCount++;
                }
            }
        }

        $this->command->info("Linked $videoCount Form Three videos as lessons and $pdfCount PDFs");
    }

    private function getSubjectFolder($subjectName)
    {
        $map = [
            'Physics' => 'Physics',
            'Chemistry' => 'Chemistry',
            'Biology' => 'Biology',
            'Mathematics' => 'Math',
            'Geography' => 'Geography',
            'English' => 'English',
            'Kiswahili' => 'Kiswahili',
            'History and Government' => 'History',
            'CRE - Christian Religious Education' => 'CRE',
            'Business Studies' => 'Business',
            'Computer Studies' => 'Computer',
            'IRE - Hindu/Indian Religious Education' => 'IRE',
        ];

        return $map[$subjectName] ?? null;
    }
}
