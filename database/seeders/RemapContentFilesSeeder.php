<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\SubStrand;
use App\Models\LearningArea;
use App\Models\CurriculumType;
use App\Models\Strand;

class RemapContentFilesSeeder extends Seeder
{
    public function run()
    {
        // Define intelligent mappings for each curriculum
        $mappings = $this->getFileMappings();

        foreach ($mappings as $curriculumName => $curricrulumMappings) {
            $curriculum = CurriculumType::where('name', $curriculumName)->first();
            if (!$curriculum) continue;

            foreach ($curricrulumMappings as $areaName => $areaMappings) {
                $area = LearningArea::where('curriculum_type_id', $curriculum->id)
                    ->where('name', $areaName)->first();
                if (!$area) continue;

                $this->remapAreaFiles($area, $areaMappings);
            }
        }

        $this->command->info('Content files remapped to correct sub-strands');
    }

    private function remapAreaFiles($area, $mappings)
    {
        // Get all sub-strands for this area
        $allSubStrands = [];
        foreach ($area->strands as $strand) {
            foreach ($strand->subStrands as $subStrand) {
                $allSubStrands[$subStrand->name] = $subStrand->id;
            }
        }

        // Get files currently assigned to this area
        $files = ContentFile::where('contentable_type', 'App\\Models\\SubStrand')
            ->whereIn('contentable_id', array_values($allSubStrands))
            ->get();

        foreach ($files as $file) {
            $filename = strtolower(basename($file->file_path));
            $ext = strtolower(substr(strrchr($file->file_path, '.'), 1));

            // PDFs go to first sub-strand only
            if ($ext === 'pdf') {
                $file->contentable_id = reset($allSubStrands);
                $file->save();
                continue;
            }

            // Videos and interactives: find matching sub-strand
            foreach ($mappings as $filePattern => $subStrandName) {
                if ($this->filenameMatches($filename, $filePattern) && isset($allSubStrands[$subStrandName])) {
                    $file->contentable_id = $allSubStrands[$subStrandName];
                    $file->save();
                    break;
                }
            }
        }
    }

    private function filenameMatches($filename, $pattern)
    {
        $patterns = explode('|', str_replace(', ', '|', $pattern));
        foreach ($patterns as $p) {
            if (strpos($filename, strtolower(trim($p))) !== false) {
                return true;
            }
        }
        return false;
    }

    private function getFileMappings()
    {
        return [
            'PP1' => [
                'Mathematical Activities' => [
                    'sorting, grouping' => 'Sorting and Grouping',
                    'matching, pairing' => 'Matching and Pairing',
                    'ordering' => 'Ordering',
                    'counting, rote' => 'Counting to 10',
                    'number recognition' => 'Number Recognition',
                    'concrete' => 'Counting Concrete Objects',
                    'sequencing, sequence' => 'Number Sequencing',
                    'sides, corner, shape, line' => 'Sides of Objects',
                    'heavy, light, mass' => 'Mass (Heavy and Light)',
                    'capacity' => 'Capacity',
                ],
                'Language Activities' => [
                    'listening' => 'Active Listening',
                    'speaking, express' => 'Self-Expression',
                    'polite, greet' => 'Polite Language',
                    'book, handling' => 'Book Handling',
                    'posture' => 'Reading Posture',
                    'writing' => 'Pre-Writing Skills',
                ],
                'Creative Activities' => [
                    'modelling, model' => 'Modelling',
                    'colour, color' => 'Colouring',
                    'dot, dots' => 'Joining Dots',
                    'sound, music' => 'Musical Sounds Identification',
                    'singing, song' => 'Singing Games',
                ],
                'Environmental Activities' => [
                    'living, animal, plant' => 'Living and Non-Living Things',
                    'family, member' => 'Family Members, Plants and Animals',
                ],
                'CRE - Christian Religious Education' => [
                    'god, creator, creation' => 'Our God',
                    'bible, story' => 'God Our Loving Father',
                    'love, neighbour, sharing' => 'Love for God',
                ],
                'HRE - Hindu Religious Education' => [
                    'paramatma, trimurti' => 'Paramatma as Trimurti',
                    'greeting, practice' => 'Forms of Greetings',
                    'worship, chant' => 'Protocols in Worship',
                ],
            ],
            'PP2' => [
                'Mathematical Activities' => [
                    'sorting, grouping' => 'Sorting and Grouping',
                    'matching, pairing' => 'Matching and Pairing',
                    'ordering' => 'Ordering',
                    'counting, rote' => 'Counting to 10',
                    'number, recognition' => 'Number Recognition',
                    'concrete' => 'Counting Concrete Objects',
                    'sequencing, sequence' => 'Number Sequencing',
                    'sides, corner, shape' => 'Sides of Objects',
                    'heavy, light, mass' => 'Mass (Heavy and Light)',
                    'capacity' => 'Capacity',
                    'addition, subtract' => 'Number Sequencing',
                ],
                'Language Activities' => [
                    'listening, listen' => 'Active Listening',
                    'speaking, express' => 'Self-Expression',
                    'polite, greet' => 'Polite Language',
                ],
            ],
            'Grade One' => [
                'English Language' => [
                    'letter, sound, alphabet, vowel, consonant' => 'Letter formation',
                    'word, comprehension' => 'Comprehension',
                    'listen, hearing' => 'Listening comprehension',
                    'speak, oral' => 'Speaking clearly',
                ],
                'Mathematics' => [
                    'number, count, recognition' => 'Number recognition',
                    'addition, add' => 'Addition and subtraction',
                    'subtraction, subtract' => 'Addition and subtraction',
                    'shape, geometry' => 'Shapes',
                    'length, long, short' => 'Length',
                    'mass, heavy, light' => 'Mass',
                    'capacity, measure' => 'Capacity',
                ],
                'Kiswahili Language' => [
                    'letter, alphabet' => 'Writing practice',
                    'word, recognition' => 'Word recognition',
                ],
            ],
        ];
    }
}
