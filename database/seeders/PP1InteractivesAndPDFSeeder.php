<?php

namespace Database\Seeders;

use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use Illuminate\Database\Seeder;

class PP1InteractivesAndPDFSeeder extends Seeder
{
    public function run(): void
    {
        $videoType = ContentType::where('name', 'Video')->first();
        $htmlType = ContentType::where('name', 'Interactive')->first();
        $pdfType = ContentType::where('name', 'PDF')->first();

        // ===== MATH CONTENT =====
        // Math Interactives - linked to all Math sub-strands
        $mathInteractivesPath = '/media/tele/ARISE1/PP1/Math/Interactives/Mobile friendly';
        $mathSubStrands = SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA001');
        })->get();

        if (is_dir($mathInteractivesPath)) {
            $interactives = glob($mathInteractivesPath . '/*.html');
            $order = 1;
            foreach ($interactives as $interactive) {
                if (file_exists($interactive)) {
                    // Link to first math sub-strand for now
                    if ($mathSubStrands->count() > 0) {
                        ContentFile::create([
                            'contentable_type' => 'App\Models\SubStrand',
                            'contentable_id' => $mathSubStrands[0]->id,
                            'content_type_id' => $htmlType->id,
                            'title' => basename($interactive, '.html'),
                            'description' => 'Interactive learning activity for mathematics',
                            'file_path' => $interactive,
                            'file_size' => filesize($interactive),
                            'is_published' => true,
                            'order' => $order++
                        ]);
                    }
                }
            }
        }

        // Math Videos - link to appropriate sub-strands
        $mathPath = '/media/tele/ARISE1/PP1/Math';
        if (is_dir($mathPath)) {
            $videos = glob($mathPath . '/*.mp4');
            foreach ($videos as $video) {
                if (file_exists($video)) {
                    // Link videos to different math sub-strands
                    $videoName = strtolower(basename($video, '.mp4'));

                    // Big/Small → Pre-Number
                    if (strpos($videoName, 'big') !== false || strpos($videoName, 'small') !== false) {
                        $subStrand = $mathSubStrands->first();
                    }
                    // Heavy/Light → Measurement
                    elseif (strpos($videoName, 'heavy') !== false || strpos($videoName, 'light') !== false) {
                        $subStrand = $mathSubStrands->where('code', '3.1')->first() ?? $mathSubStrands[2] ?? null;
                    }
                    // Ascending/Descending, Tall/Short → Numbers
                    else {
                        $subStrand = $mathSubStrands->where('code', '2.1')->first() ?? $mathSubStrands[1] ?? null;
                    }

                    if ($subStrand && $videoType) {
                        ContentFile::create([
                            'contentable_type' => 'App\Models\SubStrand',
                            'contentable_id' => $subStrand->id,
                            'content_type_id' => $videoType->id,
                            'title' => basename($video, '.mp4'),
                            'description' => 'Mathematics learning video from PP1 curriculum',
                            'file_path' => $video,
                            'file_size' => filesize($video),
                            'is_published' => true,
                            'order' => 1
                        ]);
                    }
                }
            }
        }

        // Math Curriculum PDF
        $mathPdf = '/media/tele/ARISE1/PP1/MATHEMATICS ACTIVITIES.pdf';
        if (file_exists($mathPdf) && $pdfType && $mathSubStrands->count() > 0) {
            ContentFile::create([
                'contentable_type' => 'App\Models\SubStrand',
                'contentable_id' => $mathSubStrands->first()->id,
                'content_type_id' => $pdfType->id,
                'title' => 'Mathematics Activities Curriculum Document',
                'description' => 'Complete curriculum design for PP1 Mathematical Activities',
                'file_path' => $mathPdf,
                'file_size' => filesize($mathPdf),
                'is_published' => true,
                'order' => 1
            ]);
        }

        // ===== LANGUAGE CONTENT =====
        // Language Interactives - linked to Language sub-strands
        $langInteractivesPath = '/media/tele/ARISE1/PP1/English/Interactives';
        $langSubStrands = SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA002');
        })->get();

        if (is_dir($langInteractivesPath)) {
            $interactives = glob($langInteractivesPath . '/*.html');
            $order = 1;
            foreach ($interactives as $interactive) {
                if (file_exists($interactive)) {
                    // Link to first language sub-strand
                    if ($langSubStrands->count() > 0) {
                        ContentFile::create([
                            'contentable_type' => 'App\Models\SubStrand',
                            'contentable_id' => $langSubStrands[0]->id,
                            'content_type_id' => $htmlType->id,
                            'title' => basename($interactive, '.html'),
                            'description' => 'Interactive learning activity for language',
                            'file_path' => $interactive,
                            'file_size' => filesize($interactive),
                            'is_published' => true,
                            'order' => $order++
                        ]);
                    }
                }
            }
        }

        // Language Videos
        $langPath = '/media/tele/ARISE1/PP1/English';
        if (is_dir($langPath)) {
            $videos = glob($langPath . '/*.mp4');
            foreach ($videos as $video) {
                if (file_exists($video)) {
                    if ($langSubStrands->count() > 0 && $videoType) {
                        ContentFile::create([
                            'contentable_type' => 'App\Models\SubStrand',
                            'contentable_id' => $langSubStrands[0]->id,
                            'content_type_id' => $videoType->id,
                            'title' => basename($video, '.mp4'),
                            'description' => 'Language learning video from PP1 curriculum',
                            'file_path' => $video,
                            'file_size' => filesize($video),
                            'is_published' => true,
                            'order' => 1
                        ]);
                    }
                }
            }
        }

        // Language Curriculum PDF
        $langPdf = '/media/tele/ARISE1/PP1/PP1 LANGUAGES ACTIVITIES.pdf';
        if (file_exists($langPdf) && $pdfType && $langSubStrands->count() > 0) {
            ContentFile::create([
                'contentable_type' => 'App\Models\SubStrand',
                'contentable_id' => $langSubStrands->first()->id,
                'content_type_id' => $pdfType->id,
                'title' => 'Language Activities Curriculum Document',
                'description' => 'Complete curriculum design for PP1 Language Activities',
                'file_path' => $langPdf,
                'file_size' => filesize($langPdf),
                'is_published' => true,
                'order' => 1
            ]);
        }

        // ===== CREATIVE ACTIVITIES PDF =====
        $creativeSubStrands = SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA003');
        })->get();

        $creativePdf = '/media/tele/ARISE1/PP1/PP1 CREATIVE ACITIVITIES.pdf';
        if (file_exists($creativePdf) && $pdfType && $creativeSubStrands->count() > 0) {
            ContentFile::create([
                'contentable_type' => 'App\Models\SubStrand',
                'contentable_id' => $creativeSubStrands->first()->id,
                'content_type_id' => $pdfType->id,
                'title' => 'Creative Activities Curriculum Document',
                'description' => 'Complete curriculum design for PP1 Creative Activities',
                'file_path' => $creativePdf,
                'file_size' => filesize($creativePdf),
                'is_published' => true,
                'order' => 1
            ]);
        }

        // ===== ENVIRONMENTAL ACTIVITIES PDF =====
        $envSubStrands = SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA004');
        })->get();

        $envPdf = '/media/tele/ARISE1/PP1/PP1 ENVIRONMENTAL ACTIVITIES.pdf';
        if (file_exists($envPdf) && $pdfType && $envSubStrands->count() > 0) {
            ContentFile::create([
                'contentable_type' => 'App\Models\SubStrand',
                'contentable_id' => $envSubStrands->first()->id,
                'content_type_id' => $pdfType->id,
                'title' => 'Environmental Activities Curriculum Document',
                'description' => 'Complete curriculum design for PP1 Environmental Activities',
                'file_path' => $envPdf,
                'file_size' => filesize($envPdf),
                'is_published' => true,
                'order' => 1
            ]);
        }

        // ===== CRE PDF =====
        $creSubStrands = SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA005');
        })->get();

        $crePdf = '/media/tele/ARISE1/PP1/CRE.pdf';
        if (file_exists($crePdf) && $pdfType && $creSubStrands->count() > 0) {
            ContentFile::create([
                'contentable_type' => 'App\Models\SubStrand',
                'contentable_id' => $creSubStrands->first()->id,
                'content_type_id' => $pdfType->id,
                'title' => 'CRE Curriculum Document',
                'description' => 'Complete curriculum design for PP1 Christian Religious Education',
                'file_path' => $crePdf,
                'file_size' => filesize($crePdf),
                'is_published' => true,
                'order' => 1
            ]);
        }

        // ===== HRE PDF =====
        $hreSubStrands = SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA006');
        })->get();

        $hrePdf = '/media/tele/ARISE1/PP1/IRE.pdf';
        if (file_exists($hrePdf) && $pdfType && $hreSubStrands->count() > 0) {
            ContentFile::create([
                'contentable_type' => 'App\Models\SubStrand',
                'contentable_id' => $hreSubStrands->first()->id,
                'content_type_id' => $pdfType->id,
                'title' => 'HRE Curriculum Document',
                'description' => 'Complete curriculum design for PP1 Hindu Religious Education',
                'file_path' => $hrePdf,
                'file_size' => filesize($hrePdf),
                'is_published' => true,
                'order' => 1
            ]);
        }
    }
}
