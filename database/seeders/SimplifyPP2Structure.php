<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class SimplifyPP2Structure extends Seeder
{
    public function run()
    {
        $this->command->info('=== SIMPLIFYING PP2 ===');
        $this->command->info('Converting: Subject → Strand → SubStrand → Content');
        $this->command->info('To:         Subject → Lesson (Video/HTML directly)');
        $this->command->info('');

        $subjects = LearningArea::where('grade_level', 'PP2')->get();

        foreach ($subjects as $subject) {
            $this->command->info("Processing: {$subject->name}");

            // Collect all content files
            $contentFiles = collect();
            foreach ($subject->strands()->get() as $strand) {
                foreach ($strand->subStrands()->get() as $subStrand) {
                    foreach ($subStrand->contentFiles()->get() as $contentFile) {
                        $contentFiles->push($contentFile);
                    }
                }
            }

            $this->command->line("  Found {$contentFiles->count()} content files");

            // Delete old strands
            $strand_count = $subject->strands()->count();
            $subject->strands()->delete();
            $this->command->line("  Deleted {$strand_count} old strands");

            // Create "Lessons" strand for videos/HTML
            $strand = Strand::create([
                'learning_area_id' => $subject->id,
                'code' => $subject->code . 'LESS',
                'name' => 'Lessons',
                'order' => 1,
            ]);

            // Map videos/HTML to SubStrands
            $lessonOrder = 0;
            foreach ($contentFiles as $contentFile) {
                if (!in_array($contentFile->contentType->name, ['Video', 'Interactive'])) {
                    continue;
                }

                $lessonOrder++;
                $subStrand = SubStrand::create([
                    'strand_id' => $strand->id,
                    'code' => $strand->code . 'L' . str_pad($lessonOrder, 3, '0', STR_PAD_LEFT),
                    'name' => $contentFile->title,
                    'order' => $lessonOrder,
                ]);

                $contentFile->update([
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => SubStrand::class,
                ]);
            }

            $this->command->line("  Created {$lessonOrder} lesson SubStrands");

            // Handle PDFs
            $pdfFiles = $contentFiles->filter(fn($cf) => $cf->contentType->name === 'PDF');
            if ($pdfFiles->count() > 0) {
                $pdfStrand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $subject->code . 'PDF',
                    'name' => 'Study Materials',
                    'order' => 2,
                ]);

                $pdfSubStrand = SubStrand::create([
                    'strand_id' => $pdfStrand->id,
                    'code' => $pdfStrand->code . 'SS01',
                    'name' => 'Reference Documents',
                    'order' => 1,
                ]);

                foreach ($pdfFiles as $pdfFile) {
                    $pdfFile->update([
                        'contentable_id' => $pdfSubStrand->id,
                        'contentable_type' => SubStrand::class,
                    ]);
                }

                $this->command->line("  Created Study Materials for {$pdfFiles->count()} PDFs");
            }
        }

        $this->command->info('');
        $this->command->info('✅ PP2 simplified successfully!');
    }
}
