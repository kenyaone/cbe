<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\SubStrand;

class RemapGradeSevenSciencePDFsSeeder extends Seeder
{
    public function run()
    {
        // Map STRAND numbers to sub-strands
        // Grade Seven Science structure:
        // Strand 1: Scientific Investigation (1.1, 1.2, 1.3, 1.4)
        // Strand 2: Mixtures and Compounds (2.1, 2.2, 2.3, 2.4)
        // Strand 3: Living Things (3.1, 3.2, 3.3, 3.4, 3.5)
        // Strand 4: Force and Energy (4.1, 4.2, 4.3, 4.4, 4.5)

        $strandMappings = [
            'STRAND 1' => 'Scientific Investigation',
            'STRAND 2' => 'Mixtures and Compounds',
            'STRAND 3' => 'Living Things and Environment',
            'STRAND 4' => 'Force and Energy',
            'electrical, magnetism, magnet' => 'Force and Energy',
            'mixture, element, compound, acid, base, indicator' => 'Mixtures and Compounds',
            'reproductive, excretory, human, organism, cell' => 'Living Things and Environment',
            'observation, investigation, laboratory, safety, apparatus' => 'Scientific Investigation',
        ];

        $updated = 0;
        $skipped = 0;

        // Get all Grade Seven Science PDFs
        $pdfs = ContentFile::where('file_path', 'LIKE', '%Grade Seven%')
            ->where('file_path', 'LIKE', '%Science%')
            ->whereHas('contentType', function($q) {
                $q->where('name', 'PDF');
            })
            ->get();

        $this->command->info("Found " . $pdfs->count() . " Grade Seven Science PDFs to remap");

        foreach ($pdfs as $pdf) {
            $filename = strtoupper($pdf->title);

            $matchedStrand = null;

            // Try to match strand number first (more specific)
            if (preg_match('/STRAND\s+(\d)/', $filename, $matches)) {
                $strandNum = $matches[1];

                $strandNames = [
                    '1' => 'Scientific Investigation',
                    '2' => 'Mixtures and Compounds',
                    '3' => 'Living Things and Environment',
                    '4' => 'Force and Energy',
                ];

                if (isset($strandNames[$strandNum])) {
                    $matchedStrand = $strandNames[$strandNum];
                }
            }

            // If no strand number match, try keyword matching
            if (!$matchedStrand) {
                foreach ($strandMappings as $patterns => $strand) {
                    if (strpos($strand, 'Strand') === 0) continue; // Skip STRAND X patterns

                    $patternArray = array_map('trim', explode(',', $patterns));
                    foreach ($patternArray as $pattern) {
                        if (strpos($filename, strtoupper(trim($pattern))) !== false) {
                            $matchedStrand = $strand;
                            break;
                        }
                    }
                    if ($matchedStrand) break;
                }
            }

            if ($matchedStrand) {
                // Find the first sub-strand for this strand
                $subStrand = SubStrand::whereHas('strand', function($q) use ($matchedStrand) {
                    $q->where('name', $matchedStrand)
                      ->whereHas('learningArea', function($qa) {
                          $qa->where('grade_level', 'Grade Seven')
                            ->where('name', 'Integrated Science');
                      });
                })->orderBy('order')->first();

                if ($subStrand) {
                    $pdf->contentable_id = $subStrand->id;
                    $pdf->save();
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }

        $this->command->info("✓ Updated: $updated Science PDFs");
        $this->command->info("✓ Skipped (no match): $skipped PDFs");
        $this->command->info("");
        $this->command->info("Grade Seven Science PDFs are now distributed!");
    }
}
