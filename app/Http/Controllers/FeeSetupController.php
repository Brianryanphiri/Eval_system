<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\Term;
use Illuminate\Http\Request;

class FeeSetupController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $categories = FeeCategory::where('school_id', $schoolId)->orderBy('name')->get();

        $structures = FeeStructure::where('school_id', $schoolId)
            ->with('feeCategory', 'classroom', 'term')
            ->get();

        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('name')->get();
        $terms = Term::whereHas('academicYear', fn($q) => $q->where('school_id', $schoolId))
            ->with('academicYear')
            ->orderByDesc('id')
            ->get();

        return view('settings.fee-setup', compact('categories', 'structures', 'classrooms', 'terms'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_compulsory' => 'boolean',
        ]);

        FeeCategory::create([
            'school_id' => $request->user()->school_id,
            'name' => $data['name'],
            'is_compulsory' => $data['is_compulsory'] ?? false,
        ]);

        return back()->with('success', 'Fee category added.');
    }

    public function destroyCategory(FeeCategory $feeCategory)
    {
        abort_unless($feeCategory->school_id === auth()->user()->school_id, 403);

        $feeCategory->delete();

        return back()->with('success', 'Fee category removed.');
    }

    public function storeStructure(Request $request)
    {
        $data = $request->validate([
            'fee_category_id' => 'required|exists:fee_categories,id',
            'class_id' => 'nullable|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'amount' => 'required|numeric|min:0',
        ]);

        FeeStructure::updateOrCreate(
            [
                'school_id' => $request->user()->school_id,
                'fee_category_id' => $data['fee_category_id'],
                'class_id' => $data['class_id'] ?? null,
                'term_id' => $data['term_id'],
            ],
            ['amount' => $data['amount']]
        );

        return back()->with('success', 'Fee amount saved.');
    }

    public function destroyStructure(FeeStructure $feeStructure)
    {
        abort_unless($feeStructure->school_id === auth()->user()->school_id, 403);

        $feeStructure->delete();

        return back()->with('success', 'Fee amount removed.');
    }
}