<?php

namespace Database\Seeders;

use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use Illuminate\Database\Seeder;

class ContentFilesSeeder extends Seeder
{
    public function run(): void
    {
        $videoType = ContentType::where('name', 'Video')->first();

        // Link English videos to Language Activities > Listening and Speaking > Active Listening
        $langSubStrands = SubStrand::where('code', '1.1')
            ->whereHas('strand.learningArea', function($q) {
                $q->where('code', 'LA002');
            })->get();

        foreach ($langSubStrands as $langSubStrand) {
            $englishPath = '/media/tele/ARISE1/PP1/English';
            if (is_dir($englishPath)) {
                $videos = glob($englishPath . '/*.mp4');
                $order = 1;
                foreach (array_slice($videos, 0, 5) as $video) {
                    if (file_exists($video) && $videoType) {
                        ContentFile::create([
                            'contentable_type' => 'App\Models\SubStrand',
                            'contentable_id' => $langSubStrand->id,
                            'content_type_id' => $videoType->id,
                            'title' => basename($video, '.mp4'),
                            'description' => 'English learning video from PP1 curriculum',
                            'file_path' => $video,
                            'file_size' => filesize($video),
                            'is_published' => true,
                            'order' => $order++
                        ]);
                    }
                }
            }
        }

        // Link Math videos to Mathematical Activities > Pre-Number Activities > Sorting and Grouping
        $mathSubStrands = SubStrand::where('code', '1.1')
            ->whereHas('strand.learningArea', function($q) {
                $q->where('code', 'LA001');
            })->get();

        foreach ($mathSubStrands as $mathSubStrand) {
            $mathPath = '/media/tele/ARISE1/PP1/Math';
            if (is_dir($mathPath)) {
                $videos = glob($mathPath . '/*.mp4');
                $order = 1;
                foreach (array_slice($videos, 0, 5) as $video) {
                    if (file_exists($video) && $videoType) {
                        ContentFile::create([
                            'contentable_type' => 'App\Models\SubStrand',
                            'contentable_id' => $mathSubStrand->id,
                            'content_type_id' => $videoType->id,
                            'title' => basename($video, '.mp4'),
                            'description' => 'Mathematics learning video from PP1 curriculum',
                            'file_path' => $video,
                            'file_size' => filesize($video),
                            'is_published' => true,
                            'order' => $order++
                        ]);
                    }
                }
            }
        }
    }
}
