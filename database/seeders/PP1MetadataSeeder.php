<?php

namespace Database\Seeders;

use App\Models\SubStrand;
use App\Models\LearningOutcome;
use App\Models\Competency;
use App\Models\InquiryQuestion;
use App\Models\LearningExperience;
use App\Models\Value;
use Illuminate\Database\Seeder;

class PP1MetadataSeeder extends Seeder
{
    public function run(): void
    {
        // MATHEMATICAL ACTIVITIES - Pre-Number Activities - Sorting and Grouping
        $subStrand = SubStrand::where('code', '1.1')->first();
        if ($subStrand) {
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Identify different play objects in the immediate environment',
                'order' => 1
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'List similarities among play objects in the immediate environment',
                'order' => 2
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Tell differences among play objects in the immediate environment',
                'order' => 3
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Group play objects according to a given attribute',
                'order' => 4
            ]);

            Competency::create([
                'sub_strand_id' => $subStrand->id,
                'type' => 'Critical Thinking and Problem Solving',
                'description' => 'Learners sort and group play objects according to colour, size or shape'
            ]);

            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'Which objects do you play with at home?',
                'order' => 1
            ]);
            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'How can we group objects we play with at home?',
                'order' => 2
            ]);

            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Collect play objects from the immediate environment as they observe safety',
                'order' => 1
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Talk about play objects of different colours sizes or shapes (wood blocks, balls, toys, bottle tops)',
                'order' => 2
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'In groups, identify similarities of play objects by colour, size or shape',
                'order' => 3
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'In groups, identify differences among play objects by colour or size',
                'order' => 4
            ]);

            Value::create([
                'sub_strand_id' => $subStrand->id,
                'name' => 'Love',
                'description' => 'Learners share play objects in as they work in groups or pairs'
            ]);
        }

        // LANGUAGE ACTIVITIES - Listening and Speaking - Active Listening
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand', function($q) {
            $q->where('code', '1.0');
        })->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA002');
        })->first();

        if ($subStrand) {
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Listen attentively to stories, instructions, songs and rhymes',
                'order' => 1
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Respond appropriately to what is heard',
                'order' => 2
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Identify sounds in the environment',
                'order' => 3
            ]);

            Competency::create([
                'sub_strand_id' => $subStrand->id,
                'type' => 'Communication and Collaboration',
                'description' => 'Learners listen and respond to stories, songs and instructions'
            ]);

            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'What sounds do you hear at home?',
                'order' => 1
            ]);
            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'What do you do when someone speaks to you?',
                'order' => 2
            ]);

            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Listen to stories told or read by the teacher',
                'order' => 1
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Sing songs and recite rhymes',
                'order' => 2
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Listen to and identify environmental sounds',
                'order' => 3
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Answer questions about what they have heard',
                'order' => 4
            ]);

            Value::create([
                'sub_strand_id' => $subStrand->id,
                'name' => 'Responsibility',
                'description' => 'Listen carefully and follow instructions'
            ]);
        }

        // CREATIVE ACTIVITIES - Modelling
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand', function($q) {
            $q->where('code', '1.0');
        })->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA003');
        })->first();

        if ($subStrand) {
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Manipulate plasticine, clay and dough into different shapes',
                'order' => 1
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Make simple models from clay',
                'order' => 2
            ]);

            Competency::create([
                'sub_strand_id' => $subStrand->id,
                'type' => 'Creativity and Imagination',
                'description' => 'Learners create models using plastic materials'
            ]);

            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'What can you make from clay?',
                'order' => 1
            ]);

            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Knead plasticine, clay and dough',
                'order' => 1
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Mould plasticine into various shapes',
                'order' => 2
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Make simple 3D models from clay',
                'order' => 3
            ]);

            Value::create([
                'sub_strand_id' => $subStrand->id,
                'name' => 'Creativity',
                'description' => 'Express yourself through modelling'
            ]);
        }

        // ENVIRONMENTAL ACTIVITIES - My Immediate Environment
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand', function($q) {
            $q->where('code', '1.0');
        })->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA004');
        })->first();

        if ($subStrand) {
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Differentiate between living and non-living things in the environment',
                'order' => 1
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Identify living things in the environment',
                'order' => 2
            ]);

            Competency::create([
                'sub_strand_id' => $subStrand->id,
                'type' => 'Critical Thinking',
                'description' => 'Learners observe and classify living and non-living things'
            ]);

            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'What living things do you see around you?',
                'order' => 1
            ]);
            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'What is the difference between living and non-living things?',
                'order' => 2
            ]);

            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Take nature walks to observe living and non-living things',
                'order' => 1
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Collect and sort living and non-living things from the environment',
                'order' => 2
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Discuss characteristics of living and non-living things',
                'order' => 3
            ]);

            Value::create([
                'sub_strand_id' => $subStrand->id,
                'name' => 'Environmental Stewardship',
                'description' => 'Care for the environment and living things'
            ]);
        }

        // CRE - Creation - Our God
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand', function($q) {
            $q->where('code', '1.0');
        })->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA005');
        })->first();

        if ($subStrand) {
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Explain who God is',
                'order' => 1
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Identify characteristics of God',
                'order' => 2
            ]);

            Competency::create([
                'sub_strand_id' => $subStrand->id,
                'type' => 'Spiritual Development',
                'description' => 'Learners understand God as creator and sustainer'
            ]);

            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'Who is God?',
                'order' => 1
            ]);
            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'What do we know about God?',
                'order' => 2
            ]);

            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Listen to stories about God',
                'order' => 1
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Sing songs about God',
                'order' => 2
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Draw or paint pictures showing God\'s creation',
                'order' => 3
            ]);

            Value::create([
                'sub_strand_id' => $subStrand->id,
                'name' => 'Love for God',
                'description' => 'Show reverence and gratitude to God'
            ]);
        }

        // HRE - Creation - Myself
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand', function($q) {
            $q->where('code', '1.0');
        })->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA006');
        })->first();

        if ($subStrand) {
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Identify body parts',
                'order' => 1
            ]);
            LearningOutcome::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Understand the concept of self',
                'order' => 2
            ]);

            Competency::create([
                'sub_strand_id' => $subStrand->id,
                'type' => 'Self-Awareness',
                'description' => 'Learners know and understand themselves'
            ]);

            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'Who am I?',
                'order' => 1
            ]);
            InquiryQuestion::create([
                'sub_strand_id' => $subStrand->id,
                'question' => 'What are my body parts?',
                'order' => 2
            ]);

            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Name and touch body parts',
                'order' => 1
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Draw or paint self-portraits',
                'order' => 2
            ]);
            LearningExperience::create([
                'sub_strand_id' => $subStrand->id,
                'description' => 'Sing songs about body parts',
                'order' => 3
            ]);

            Value::create([
                'sub_strand_id' => $subStrand->id,
                'name' => 'Self-Worth',
                'description' => 'Value yourself and your uniqueness'
            ]);
        }
    }
}
