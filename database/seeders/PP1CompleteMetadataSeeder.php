<?php

namespace Database\Seeders;

use App\Models\SubStrand;
use App\Models\LearningOutcome;
use App\Models\Competency;
use App\Models\InquiryQuestion;
use App\Models\LearningExperience;
use App\Models\Value;
use Illuminate\Database\Seeder;

class PP1CompleteMetadataSeeder extends Seeder
{
    public function run(): void
    {
        // LANGUAGE ACTIVITIES - Listening and Speaking - Greetings and Farewell
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA002');
        })->first();

        if ($subStrand) {
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Mention why we greet each other in our day-to-day life', 'order' => 1]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Use greetings in social interactions', 'order' => 2]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Use farewell words and gestures in social interactions', 'order' => 3]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Appreciate the importance of greetings and bidding farewell in daily interactions', 'order' => 4]);

            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Communication and Collaboration', 'description' => 'The learner enhances listening and speaking skills while discussing pictures on people greeting and bidding farewell']);
            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Self-Efficacy', 'description' => 'The learner assertively role plays initiating and responding to greetings and bidding farewell while using words and gestures']);

            InquiryQuestion::create(['sub_strand_id' => $subStrand->id, 'question' => 'Why do we greet people?', 'order' => 1]);

            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Say why people greet each other', 'order' => 1]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Say people who have greeted them and those they have greeted', 'order' => 2]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Collaboratively imitate greetings', 'order' => 3]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Watch a video clip or listen to an audio recording on people greeting and bidding farewell', 'order' => 4]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Role play people initiating and responding to greetings', 'order' => 5]);

            Value::create(['sub_strand_id' => $subStrand->id, 'name' => 'Integrity', 'description' => 'The learner role plays greetings and bidding farewell with humility']);
            Value::create(['sub_strand_id' => $subStrand->id, 'name' => 'Unity', 'description' => 'The learner imitates greetings together with others amicably and in unison']);
        }

        // CREATIVE ACTIVITIES - Myself - Scribbling
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand', function($q) {
            $q->where('code', '1.0');
        })->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA003');
        })->first();

        if ($subStrand) {
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Identify dry media used in scribbling', 'order' => 1]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Scribble using varied dry media for fine motor development', 'order' => 2]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Sing action songs about body parts used in scribbling', 'order' => 3]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Appreciate own and others\' scribbled work', 'order' => 4]);

            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Communication and Collaboration', 'description' => 'Learner speaks clearly and confidently when naming materials used in scribbling']);
            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Self-Efficacy', 'description' => 'Learner demonstrates self-awareness when singing songs on body parts and stretching fingers, hands or feet']);

            InquiryQuestion::create(['sub_strand_id' => $subStrand->id, 'question' => 'Why do you scribble?', 'order' => 1]);
            InquiryQuestion::create(['sub_strand_id' => $subStrand->id, 'question' => 'How can one scribble?', 'order' => 2]);

            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Sing a song about body parts while stretching fingers, hands or feet', 'order' => 1]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Pick and name the dry media (coloured pencils, crayon, chalk, charcoal)', 'order' => 2]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Scribble using dry media to fill the given space of outlines', 'order' => 3]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Display scribbled pictures and comment positively on each other\'s work', 'order' => 4]);

            Value::create(['sub_strand_id' => $subStrand->id, 'name' => 'Unity', 'description' => 'Learner collaborates with others, shares dry media equitably']);
            Value::create(['sub_strand_id' => $subStrand->id, 'name' => 'Respect', 'description' => 'Learner displays humility, patience and gives positive comments on others\' artworks']);
        }

        // ENVIRONMENTAL ACTIVITIES - Myself - Self-Awareness
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA004');
        })->first();

        if ($subStrand) {
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Tell their names for identity', 'order' => 1]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Classify pictures of boys and girls for self-awareness', 'order' => 2]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Appreciate oneself for self-esteem', 'order' => 3]);

            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Communication and Collaboration', 'description' => 'Learners speak clearly when mentioning their names']);
            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Self-Efficacy', 'description' => 'Learners know who they are when grouping themselves according to boys and girls']);

            InquiryQuestion::create(['sub_strand_id' => $subStrand->id, 'question' => 'Why do people have names?', 'order' => 1]);

            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Speak clearly when mentioning their names in pairs', 'order' => 1]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Sing songs about themselves in pairs', 'order' => 2]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Group pictures of boys and girls in pairs', 'order' => 3]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Watch video clips on boys and girls', 'order' => 4]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Colour drawn pictures of boys and girls', 'order' => 5]);

            Value::create(['sub_strand_id' => $subStrand->id, 'name' => 'Respect', 'description' => 'Learners enhance patience when telling their names in turns']);
        }

        // CRE - Creation - Our God
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA005');
        })->first();

        if ($subStrand) {
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Tell the qualities of God', 'order' => 1]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Say prayers to God', 'order' => 2]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Desire to know God', 'order' => 3]);

            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Communication and Collaboration', 'description' => 'The learner can reason and show his or her opinion as they take turns talking about God']);

            InquiryQuestion::create(['sub_strand_id' => $subStrand->id, 'question' => 'Who is God?', 'order' => 1]);

            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Take turns talking about God', 'order' => 1]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Name the qualities of God such as protector, creator, loving, and provider', 'order' => 2]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Sing songs about God in groups', 'order' => 3]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Say short prayers to God', 'order' => 4]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Recite simple poems about God in groups', 'order' => 5]);

            Value::create(['sub_strand_id' => $subStrand->id, 'name' => 'Unity', 'description' => 'The learner collaborates with others to recite simple poems about God']);
        }

        // HRE - Creation - Myself
        $subStrand = SubStrand::where('code', '1.1')->whereHas('strand.learningArea', function($q) {
            $q->where('code', 'LA006');
        })->first();

        if ($subStrand) {
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Recognize self, name and gender for self-awareness', 'order' => 1]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Tell the first religious greeting as they wake up in the morning', 'order' => 2]);
            LearningOutcome::create(['sub_strand_id' => $subStrand->id, 'description' => 'Appreciate the self as a girl or a boy for self-esteem', 'order' => 3]);

            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Communication and Collaboration', 'description' => 'Learners speak clearly and confidently when mentioning their name and gender']);
            Competency::create(['sub_strand_id' => $subStrand->id, 'type' => 'Self-Efficacy', 'description' => 'Learners practice good personal care and hygiene']);

            InquiryQuestion::create(['sub_strand_id' => $subStrand->id, 'question' => 'What do you like about yourself?', 'order' => 1]);

            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Share a moment of self-introduction by mentioning their name and gender', 'order' => 1]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Colour/paint sketches of boys and girls', 'order' => 2]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Practice good personal care activities', 'order' => 3]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Listen/sing songs/play games about good health in boys and girls', 'order' => 4]);
            LearningExperience::create(['sub_strand_id' => $subStrand->id, 'description' => 'Sing songs that appreciate the importance of being a boy/girl as a gift from Paramatma', 'order' => 5]);

            Value::create(['sub_strand_id' => $subStrand->id, 'name' => 'Respect', 'description' => 'Learners enhance patience when telling their names in turns']);
        }
    }
}
