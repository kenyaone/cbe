<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use App\Models\LearningArea;

class LinkGradeTenContentSeeder extends Seeder
{
    public function run()
    {
        $basePath = '/home/tele/cbe-platform/storage/app/media/grade-ten';

        if (!is_dir($basePath)) {
            $this->command->error("Grade 10 content directory not found: $basePath");
            return;
        }

        $supportedExtensions = ['pdf', 'html', 'htm'];
        $count = 0;

        // Map directory names to subject names
        $subjectMap = [
            'Agriculture' => 'Agriculture',
            'Aviation-Technology' => 'Aviation Technology',
            'Biology' => 'Biology',
            'Building-and-Construction' => 'Building and Construction',
            'Business-Studies' => 'Business Studies',
            'Chemistry' => 'Chemistry',
            'Christian-Religious-Education' => 'Christian Religious Education',
            'Community-Service-Learning' => 'Community Service Learning',
            'Computer-Science' => 'Computer Science',
            'Economics' => 'Economics',
            'Electrical-Technology' => 'Electrical Technology',
            'English' => 'English',
            'Fine-Arts' => 'Fine Arts',
            'Geography' => 'Geography',
            'History-and-Citizenship' => 'History and Citizenship',
            'Kiswahili' => 'Kiswahili',
            'Life-Skills-Education' => 'Life Skills Education',
            'Maritime-and-Fisheries' => 'Maritime and Fisheries',
            'Mathematics' => 'Mathematics',
            'Metal-Work' => 'Metal Work',
            'Music-and-Dance' => 'Music and Dance',
            'Performing-Arts' => 'Performing Arts',
            'Physical-Education' => 'Physical Education',
            'Physics' => 'Physics',
            'Sports-Science' => 'Sports Science',
            'Technical-Applied-Technology' => 'Technical Applied Technology',
            'Visual-Arts' => 'Visual Arts',
            'Wood-Work' => 'Wood Work',
        ];

        foreach ($subjectMap as $dirName => $subjectName) {
            $subjectPath = $basePath . '/' . $dirName;

            if (!is_dir($subjectPath)) {
                continue;
            }

            // Get the subject from database
            $subject = LearningArea::where('grade_level', 'Grade Ten')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) {
                continue;
            }

            // Get the first strand and its first sub-strand
            $subStrand = $subject->strands()
                ->first()?->subStrands()
                ->first();

            if (!$subStrand) {
                continue;
            }

            // Scan for files in this subject folder
            $files = scandir($subjectPath);

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;

                $filePath = $subjectPath . '/' . $file;

                if (is_dir($filePath)) {
                    // Recursively process subdirectories
                    $this->processDirectory($filePath, $subStrand, $supportedExtensions, $count);
                } else {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                    if (!in_array($extension, $supportedExtensions)) continue;

                    $this->linkFile($filePath, $file, $extension, $subStrand, $count);
                }
            }
        }

        $this->command->info("Linked $count Grade Ten content files");
    }

    private function processDirectory($dirPath, $subStrand, $supportedExtensions, &$count)
    {
        $files = scandir($dirPath);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $filePath = $dirPath . '/' . $file;

            if (is_dir($filePath)) {
                $this->processDirectory($filePath, $subStrand, $supportedExtensions, $count);
            } else {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (!in_array($extension, $supportedExtensions)) continue;

                $this->linkFile($filePath, $file, $extension, $subStrand, $count);
            }
        }
    }

    private function linkFile($filePath, $fileName, $extension, $subStrand, &$count)
    {
        // Check if file already exists in database
        if (ContentFile::where('file_path', $filePath)->exists()) {
            return;
        }

        $title = pathinfo($fileName, PATHINFO_FILENAME);

        // Determine content type
        $contentTypeName = match($extension) {
            'pdf' => 'PDF',
            'html', 'htm' => 'Interactive',
            default => null,
        };

        if (!$contentTypeName) return;

        $contentType = ContentType::firstOrCreate(['name' => $contentTypeName]);

        ContentFile::create([
            'title' => $title,
            'file_path' => $filePath,
            'content_type_id' => $contentType->id,
            'contentable_id' => $subStrand->id,
            'contentable_type' => SubStrand::class,
            'is_published' => true,
        ]);

        $count++;
    }
}
