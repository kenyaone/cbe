<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\SubStrand;
use App\Models\Strand;
use App\Models\ContentFile;
use App\Models\ContentType;

class RemapPP1EnglishVideos extends Seeder
{
    public function run()
    {
        $this->command->info('=== REMAPPING PP1 ENGLISH VIDEOS ===');

        // Get Language Activities subject
        $langSubject = LearningArea::where('grade_level', 'PP1')
            ->where('name', 'Language Activities')
            ->first();

        if (!$langSubject) {
            $this->command->error('Language Activities subject not found');
            return;
        }

        // Ensure it has Lessons strand
        $strand = $langSubject->strands()->where('name', 'Lessons')->first();
        if (!$strand) {
            $strand = Strand::create([
                'learning_area_id' => $langSubject->id,
                'code' => $langSubject->code . 'LESS',
                'name' => 'Lessons',
                'order' => 1,
            ]);
            $this->command->line('Created Lessons strand for Language Activities');
        }

        // Patterns for English videos
        $englishPatterns = [
            'english', 'greet', 'vocabulary', 'transport', 'vowel', 'consonant',
            'magic word', 'abc', 'a-z', 'alphabet', 'family', 'polite'
        ];

        // Find English videos currently in Math
        $mathSubject = LearningArea::where('grade_level', 'PP1')
            ->where('name', 'Mathematical Activities')
            ->first();

        $movedCount = 0;
        $mathStrand = $mathSubject->strands()->where('name', 'Lessons')->first();

        if ($mathStrand) {
            foreach ($mathStrand->subStrands()->get() as $subStrand) {
                foreach ($subStrand->contentFiles()->get() as $contentFile) {
                    $lowerTitle = strtolower($contentFile->title);
                    $isEnglish = false;

                    foreach ($englishPatterns as $pattern) {
                        if (strpos($lowerTitle, $pattern) !== false) {
                            $isEnglish = true;
                            break;
                        }
                    }

                    if ($isEnglish && $contentFile->contentType->name === 'Video') {
                        // Create new SubStrand in Language Activities
                        $newSubStrand = SubStrand::create([
                            'strand_id' => $strand->id,
                            'code' => $strand->code . 'V' . str_pad($strand->subStrands()->count() + 1, 2, '0', STR_PAD_LEFT),
                            'name' => $contentFile->title,
                            'order' => $strand->subStrands()->count() + 1,
                        ]);

                        // Move content file
                        $contentFile->update([
                            'contentable_id' => $newSubStrand->id,
                            'contentable_type' => SubStrand::class,
                        ]);

                        $this->command->line("  ✓ Moved: {$contentFile->title}");
                        $movedCount++;
                    }
                }
            }
        }

        // Delete old empty SubStrands from Math
        foreach ($mathStrand->subStrands()->get() as $subStrand) {
            if ($subStrand->contentFiles()->count() === 0) {
                $subStrand->delete();
            }
        }

        $this->command->info('');
        $this->command->info("✅ Moved {$movedCount} English videos to Language Activities");
    }
}
