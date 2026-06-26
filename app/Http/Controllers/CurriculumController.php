<?php

namespace App\Http\Controllers;

use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CurriculumController extends Controller
{
    public function index()
    {
        $curriculumTypes = CurriculumType::all();
        return view('curriculum.index', compact('curriculumTypes'));
    }

    public function showType($type)
    {
        $curriculumType = CurriculumType::where('name', $type)->firstOrFail();
        $learningAreas = $curriculumType->learningAreas()->orderBy('order')->get();
        return view('curriculum.type', compact('curriculumType', 'learningAreas'));
    }

    public function showArea($typeId, $areaId)
    {
        $learningArea = LearningArea::findOrFail($areaId);
        $strands = $learningArea->strands()->orderBy('order')->get();
        return view('curriculum.area', compact('learningArea', 'strands'));
    }

    public function showStrand($typeId, $areaId, $strandId)
    {
        $strand = Strand::findOrFail($strandId);
        $subStrands = $strand->subStrands()->orderBy('order')->get();
        return view('curriculum.strand', compact('strand', 'subStrands'));
    }

    public function showSubStrand($typeId, $areaId, $strandId, $subStrandId)
    {
        $subStrand = SubStrand::findOrFail($subStrandId);
        $contentFiles = $subStrand->contentFiles()->orderBy('order')->get();
        return view('curriculum.sub-strand', compact('subStrand', 'contentFiles'));
    }
}
