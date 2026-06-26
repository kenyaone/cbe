<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\SubStrand;

class RemapGradeSevenPDFsSeeder extends Seeder
{
    public function run()
    {
        // Map specific Grade Seven PDFs to their correct sub-strands
        $pdfMappings = [
            // Whole Numbers (1.1)
            'place value, place and total, reading and writing, rounding, classify, odd, even, prime, composite, divisibility, lcm, gcd' => '1.1 Whole numbers',

            // Fractions and Decimals (1.2)
            'fraction, improper, mixed, reciprocal, ordering, adding, subtracting, decimal, division, percent, percentage' => '1.2 Fractions and decimals',

            // Integers (1.3)
            'integer, negative, positive, inequality, compound' => '1.3 Integers',

            // Operations (1.4)
            'multiplication, multiply, division, divide, operation' => '1.4 Operations',

            // Length and Distance (2.1)
            'length, metre, centimetre, kilometer, mm, hectometer, decameter, conversion, distance, measurement, ruler' => '2.1 Length and distance',

            // Mass and Weight (2.2)
            'mass, weight, kilogram, gram' => '2.2 Mass and weight',

            // Area and Perimeter (2.3)
            'area, perimeter, circumference, radius, rhombus, parallelogram, trapezoid, polygon, sector, combined' => '2.3 Area and perimeter',

            // Volume and Capacity (2.4)
            'volume, capacity, litre, cubic, cylinder, cone, sphere, cuboid, cube' => '2.4 Volume and capacity',

            // Time (2.5)
            'time, clock, hour, minute, second, duration, speed, conversion, travel' => '2.5 Time',

            // 2D Shapes (3.1)
            'shape, 2d, dimension, triangle, square, circle, polygon, ellipse, line' => '3.1 2D Shapes',

            // 3D Shapes (3.2)
            '3d, shape' => '3.2 3D Shapes',

            // Angles (3.3)
            'angle, angles, degree, transversal, straight, point, parallel, sides, perpendicular' => '3.3 Angles',

            // Coordinates (3.4)
            'coordinate, plane, graph' => '3.4 Coordinates',

            // Transformations (3.5)
            'transformation, rotation, reflection, translation, symmetry' => '3.5 Transformations',

            // Patterns (4.1)
            'pattern, sequence, series' => '4.1 Patterns',

            // Variables and Expressions (4.2)
            'variable, expression, algebraic, forming' => '4.2 Variables and expressions',

            // Equations (4.3)
            'equation, linear, solve, equality' => '4.3 Equations',

            // Functions (4.4)
            'function, relation' => '4.4 Functions',

            // Data Collection and Organization (5.1)
            'data, collect, frequency, table' => '5.1 Data collection and organization',

            // Data Representation (5.2)
            'graph, chart, pictograph, bar, pie, line' => '5.2 Data representation',

            // Data Interpretation (5.3)
            'interpret' => '5.3 Data interpretation',

            // Probability (5.4)
            'probability, chance, likely, possible, outcome' => '5.4 Probability',
        ];

        $updated = 0;
        $skipped = 0;

        // Get all Grade Seven Mathematics PDFs
        $pdfs = ContentFile::where('file_path', 'LIKE', '%Grade Seven%')
            ->where('file_path', 'LIKE', '%Math%')
            ->whereHas('contentType', function($q) {
                $q->where('name', 'PDF');
            })
            ->get();

        $this->command->info("Found " . $pdfs->count() . " Grade Seven Math PDFs to remap");

        foreach ($pdfs as $pdf) {
            $filename = strtolower($pdf->title);

            // Find matching sub-strand
            $matched = false;
            foreach ($pdfMappings as $patterns => $subStrandName) {
                $patternArray = array_map('trim', explode(',', $patterns));
                foreach ($patternArray as $pattern) {
                    if (strpos($filename, strtolower(trim($pattern))) !== false) {
                        // Find the sub-strand
                        $subStrand = SubStrand::where('name', $subStrandName)
                            ->whereHas('strand.learningArea', function($q) {
                                $q->where('grade_level', 'Grade Seven')
                                  ->where('name', 'Mathematics');
                            })
                            ->first();

                        if ($subStrand) {
                            $pdf->contentable_id = $subStrand->id;
                            $pdf->save();
                            $updated++;
                            $matched = true;
                            break;
                        }
                    }
                }
                if ($matched) break;
            }

            if (!$matched) {
                $skipped++;
            }
        }

        $this->command->info("✓ Updated: $updated PDFs");
        $this->command->info("✓ Skipped (no match): $skipped PDFs");
        $this->command->info("");
        $this->command->info("Grade Seven Mathematics PDFs are now distributed across sub-strands!");
    }
}
