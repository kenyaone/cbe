<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\SubStrand;

class UpdateGradeNineFilePaths extends Seeder
{
    public function run()
    {
        $updated = 0;

        $files = ContentFile::whereHas('contentable.strand.learningArea', function($q) {
            $q->where('grade_level', 'Grade Nine');
        })->get();

        foreach ($files as $file) {
            if (strpos($file->file_path, '/media/tele/CBE/Grade Nine Complete') === 0) {
                // Replace USB path with local path
                $relativePath = str_replace('/media/tele/CBE/Grade Nine Complete/', '', $file->file_path);
                $newPath = storage_path('app/media/grade-nine/' . $relativePath);

                if (file_exists($newPath)) {
                    $file->update(['file_path' => $newPath]);
                    $updated++;
                }
            }
        }

        $this->command->info("Updated $updated Grade Nine file paths to local storage");
    }
}
