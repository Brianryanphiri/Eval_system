<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function index(Request $request)
    {
        $studentRecord = $request->user()->studentRecord;

        if (!$studentRecord) {
            abort(404, 'No student record is linked to this account.');
        }

        // Reuses the existing Student Profile view — same page a teacher/admin sees for this student
        return redirect()->route('students.show', $studentRecord);
    }

    // Somewhere in the class data assembly, filter based on Peer Comparison:
    // $classStats = [
    //     'show_rank'       => \App\Services\PeerComparison::showRank(),
    //     'show_percentile' => \App\Services\PeerComparison::showPercentile(),
    //     'show_avg'        => \App\Services\PeerComparison::showClassAverage(),
    //     'hide_names'      => \App\Services\PeerComparison::hideNames(),
    //     'meets_minimum'   => \App\Services\PeerComparison::meetsMinimumSize(
    //         $student->currentClassroom()?->currentStudents()->count() ?? 0
    //     ),
    // ];
}