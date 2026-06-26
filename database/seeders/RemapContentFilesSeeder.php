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

        // Sort mappings by pattern length (longest first = most specific)
        uasort($mappings, function($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($files as $file) {
            $filename = strtolower(basename($file->file_path));
            $ext = strtolower(substr(strrchr($file->file_path, '.'), 1));

            // Find first (most specific) matching sub-strand
            $found = false;
            foreach ($mappings as $filePattern => $subStrandName) {
                if ($this->filenameMatches($filename, $filePattern) && isset($allSubStrands[$subStrandName])) {
                    $file->contentable_id = $allSubStrands[$subStrandName];
                    $file->save();
                    $found = true;
                    break;
                }
            }

            // If no match found, assign to first sub-strand (fallback)
            if (!$found) {
                $file->contentable_id = reset($allSubStrands);
                $file->save();
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
            'Grade Two' => [
                'English Language' => [
                    'noun, nouns, singular, plural' => 'Nouns',
                    'verb, verbs, tense, continuous' => 'Verbs',
                    'adverb, adjective' => 'Adjectives',
                    'preposition, position' => 'Parts of speech',
                    'possessive, pronoun' => 'Parts of speech',
                    'rhyming, rhyme' => 'Comprehension',
                    'ing, adding' => 'Verbs',
                    'transport, modes' => 'Comprehension',
                    'shape, shapes' => 'Comprehension',
                ],
                'Mathematics' => [
                    'addition, adding, add' => 'Addition',
                    'subtraction, subtract, subtracting' => 'Subtraction',
                    'multiplication, multiply' => 'Multiplication',
                    'division, divide' => 'Division',
                    'number, counting, count' => 'Number recognition',
                    'shape, 2d, 3d' => '2D Shapes',
                    'length, metre, centimetre' => 'Length',
                    'mass, weight, heavy, light' => 'Mass',
                    'capacity, litre, volume' => 'Capacity',
                    'calendar, time, month, day, week' => 'Time and Calendar',
                ],
            ],
            'Grade Three' => [
                'English Language' => [
                    'tense, verb, grammar' => 'Tenses',
                    'paragraph, writing' => 'Paragraph writing',
                    'letter' => 'Letter writing',
                    'comprehension' => 'Comprehension',
                    'vocabulary' => 'Vocabulary building',
                ],
                'Mathematics' => [
                    'addition, adding, add' => 'Addition',
                    'subtraction, subtract, subtracting' => 'Subtraction',
                    'multiplication, multiply' => 'Multiplication',
                    'division, divide' => 'Division',
                    'number, counting, count' => 'Number recognition',
                    'shape, 2d, 3d' => '2D Shapes',
                    'length, metre, centimetre' => 'Length',
                    'mass, weight, heavy, light' => 'Mass',
                    'capacity, litre, volume' => 'Capacity',
                    'time, clock, hour, minute' => 'Time',
                    'money, shilling, coin' => 'Money',
                    'data' => 'Collecting data',
                ],
            ],
            'Grade Four' => [
                'English Language' => [
                    'english, grammar, learning, game' => 'Grammar and Vocabulary',
                ],
                'Kiswahili Language' => [
                    'kiswahili, gredi, learning, game' => 'Oral Skills',
                ],
                'Mathematics' => [
                    'math, mathematics, secure' => 'Numbers and Operations',
                ],
                'Science and Technology' => [
                    'science, agriculture, nutrition, technology' => 'Life sciences',
                ],
                'Christian Religious Education' => [
                    'christian, cre' => 'God and Creation',
                ],
                'Creative Activities' => [
                    'creative, arts, quiz' => 'Visual Arts',
                ],
            ],
            'Grade Five' => [
                'English Language' => [
                    'tense, continuous, past' => 'Tenses',
                    'noun, nouns, singular, plural' => 'Parts of speech',
                    'verb, verbs, action' => 'Parts of speech',
                    'adjective, adverb' => 'Parts of speech',
                    'preposition, pronoun' => 'Parts of speech',
                    'punctuation, comma, period' => 'Punctuation',
                    'comprehension, reading' => 'Comprehension',
                    'vocabulary, word' => 'Vocabulary building',
                    'writing, essay, paragraph' => 'Essay writing',
                ],
                'Mathematics' => [
                    'fraction, numerator, denominator, improper, mixed' => 'Fractions',
                    'decimal' => 'Decimals',
                    'angle' => 'Angles',
                    'place value, digit' => 'Place values',
                    'area, perimeter' => 'Area and Perimeter',
                    'addition, adding, add' => 'Addition',
                    'subtraction, subtract, subtracting' => 'Subtraction',
                    'multiplication, multiply, times' => 'Multiplication',
                    'division, divide' => 'Division',
                    'number, counting, count' => 'Number recognition',
                    'shape, 2d, 3d' => '2D Shapes',
                    'length, metre, centimetre' => 'Length',
                    'mass, weight, heavy, light' => 'Mass',
                    'capacity, litre, volume' => 'Capacity',
                    'time, clock, hour, minute' => 'Time',
                    'money, shilling, coin' => 'Money',
                    'data, graph, chart' => 'Collecting data',
                ],
                'Science and Technology' => [
                    'science, life, animal, plant, ecosystem' => 'Life sciences',
                ],
            ],
            'Grade Six' => [
                'English Language' => [
                    'tense, continuous, past' => 'Tenses',
                    'noun, nouns, singular, plural' => 'Parts of speech',
                    'verb, verbs, action' => 'Parts of speech',
                    'adjective, adverb' => 'Parts of speech',
                    'preposition, pronoun' => 'Parts of speech',
                    'punctuation, comma, period' => 'Punctuation',
                    'comprehension, reading, understand' => 'Comprehension',
                    'vocabulary, word, vocabulary' => 'Vocabulary building',
                    'writing, essay, paragraph, composition' => 'Essay writing',
                    'listening, speaking' => 'Speaking and Listening',
                ],
                'Mathematics' => [
                    'fraction, numerator, denominator, improper, mixed' => 'Fractions',
                    'decimal, decimal' => 'Decimals',
                    'angle, angles' => 'Angles',
                    'place value, digit, whole' => 'Place values',
                    'area, perimeter, circumference' => 'Area and Perimeter',
                    'addition, adding, add, sum' => 'Operations',
                    'subtraction, subtract, subtracting, difference' => 'Operations',
                    'multiplication, multiply, times, product' => 'Operations',
                    'division, divide, quotient' => 'Operations',
                    'number, counting, count, integers, whole' => 'Numbers',
                    'shape, 2d, 3d, dimension, triangle, square, circle, rectangle, cone, cylinder' => '2D Shapes',
                    'length, metre, centimetre, km, mm' => 'Length',
                    'mass, weight, heavy, light, gram, kilogram' => 'Mass',
                    'capacity, litre, volume, millilitre' => 'Capacity',
                    'time, clock, hour, minute, second, duration' => 'Time',
                    'money, shilling, coin, currency' => 'Money',
                    'data, graph, chart, statistics, probability' => 'Data Handling',
                    'pattern, algebra, variable, expression' => 'Algebra',
                    'symmetry, transformation, rotation, reflection' => 'Transformations',
                ],
                'Integrated Science' => [
                    'plant, animal, ecosystem, living, organism' => 'Life Sciences',
                    'matter, force, energy, motion, speed' => 'Physical Sciences',
                    'weather, climate, water, cycle, atmosphere' => 'Earth Sciences',
                    'design, technology, innovation, material' => 'Technology',
                ],
            ],
            'Grade Seven' => [
                'English' => [
                    'tense, mood, continuous, past' => 'Tenses and moods',
                    'noun, nouns, singular, plural, pronoun' => 'Parts of speech',
                    'verb, verbs, action, auxiliary' => 'Parts of speech',
                    'adjective, adverb, article' => 'Parts of speech',
                    'preposition, conjunction, interjection' => 'Parts of speech',
                    'punctuation, comma, period, colon, semicolon' => 'Sentence structure',
                    'comprehension, reading, understand' => 'Comprehension',
                    'vocabulary, word, vocabulary, idiom' => 'Vocabulary building',
                    'writing, essay, paragraph, composition, report' => 'Essay writing',
                    'speaking, presentation, discussion, oral' => 'Speaking and Listening',
                    'listening, listen, comprehend' => 'Speaking and Listening',
                ],
                'Mathematics' => [
                    'fraction, numerator, denominator, improper, mixed, reciprocal' => 'Fractions and decimals',
                    'decimal, percent, percentage, decimal' => 'Fractions and decimals',
                    'angle, angles, degree, transversal, straight, point, parallel' => 'Angles',
                    'integer, negative, positive, inequality, compound' => 'Integers',
                    'coordinate, plane, graph, travel, distance' => 'Coordinates',
                    'transformation, rotation, reflection, translation, symmetry' => 'Transformations',
                    'area, perimeter, circumference, radius, sector, rhombus, parallelogram, trapezoid, polygon' => 'Area and perimeter',
                    'volume, capacity, litre, cubic, cylinder, cone, sphere, cuboid' => 'Volume and capacity',
                    'addition, adding, add, sum' => 'Operations',
                    'subtraction, subtract, difference' => 'Operations',
                    'multiplication, multiply, times, product' => 'Operations',
                    'division, divide, quotient' => 'Operations',
                    'number, counting, count, whole, prime, odd, even, composite, divisibility' => 'Whole numbers',
                    'shape, 2d, 3d, dimension, triangle, square, circle, polygon, ellipse' => '2D Shapes',
                    'length, metre, centimetre, kilometer, mm, hectometer, decameter, conversion' => 'Length and distance',
                    'mass, weight, kilogram, gram' => 'Mass and weight',
                    'time, clock, hour, minute, second, duration, speed, conversion' => 'Time',
                    'data, graph, chart, statistics, frequency, pictograph, bar, pie, table' => 'Data Handling',
                    'probability, chance, likely, possible, outcome' => 'Probability',
                    'pattern, sequence, series' => 'Patterns',
                    'variable, expression, equation, solve, algebraic, linear, formation' => 'Variables and expressions',
                    'function, relation' => 'Functions',
                ],
                'Integrated Science' => [
                    'observation, experiment, method, procedure, investigation, laboratory, apparatus, safety' => 'Scientific Investigation',
                    'matter, property, physical, chemical' => 'Mixtures and Compounds',
                    'mixture, element, compound, pure, solution, acid, base, indicator' => 'Mixtures and Compounds',
                    'reaction, combine, decompose' => 'Mixtures and Compounds',
                    'cell, organism, plant, animal, living, reproduction, reproductive, excretory, human' => 'Living Things and Environment',
                    'ecosystem, environment, habitat, community, species' => 'Living Things and Environment',
                    'adaptation, inherited, environment, survival' => 'Living Things and Environment',
                    'interdependence, food chain, food web, predator, prey' => 'Living Things and Environment',
                    'force, push, pull, friction, gravity, motion, speed, distance' => 'Force and Energy',
                    'motion, speed, velocity, acceleration' => 'Force and Energy',
                    'machine, lever, pulley, incline, wedge' => 'Force and Energy',
                    'energy, heat, light, sound, kinetic, potential, temperature, kelvin' => 'Force and Energy',
                    'electricity, current, circuit, conductor, insulator' => 'Force and Energy',
                    'magnet, magnetic, magnetism, field, electrical' => 'Force and Energy',
                ],
            ],
        ];
    }
}
