<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkingPaper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Controller
{

    use AuthorizesRequests;

    /**
     * Display admin dashbard with list of working papers
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $status = $request->query('status');
        $financialYear = $request->query('year');
        $search = $request->query('search');

        // Query working papers
        $query = WorkingPaper::with(['user', 'reviewer'])
            ->orderBy('updated_at', 'desc');

        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }

        if ($financialYear) {
            $query->where('financial_year', $financialYear);
        }

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $workingPapers = $query->paginate(20);

        // Get counts for status badges
        $statusCounts = [
            'all' => WorkingPaper::count(),
            'submitted' => WorkingPaper::where('status', 'submitted')->count(),
            'resubmitted' => WorkingPaper::where('status', 'resubmitted')->count(),
            'approved' => WorkingPaper::where('status', 'approved')->count(),
            'rejected' => WorkingPaper::where('status', 'rejected')->count(),
            'draft' => WorkingPaper::where('status', 'draft')->count(),
        ];

        // Get pending count (submitted + resubmitted)
        $pendingCount = WorkingPaper::whereIn('status', ['submitted', 'resubmitted'])->count();

        // Get available financial years
        $financialYears = WorkingPaper::select('financial_year')
            ->distinct()
            ->orderBy('financial_year', 'desc')
            ->pluck('financial_year');

        return view('admin.dashboard', [
            'workingPapers' => $workingPapers,
            'statusCounts' => $statusCounts,
            'pendingCount' => $pendingCount,
            'financialYears' => $financialYears,
            'currentStatus' => $status,
            'currentYear' => $financialYear,
            'searchQuery' => $search,
        ]);
    }

    /**
     * Display specific working paper for review
     */
    public function show(WorkingPaper $workingPaper)
    {
        $this->authorize('view', $workingPaper);

        // Load all related data
        $workingPaper->load([
            'user',
            'reviewer',
            'wageData',
            'rentalProperties.incomeItems',
            'rentalProperties.expenseItems',
            'incomeItems',
            'expenseItems',
        ]);

        return view('admin.review', [
            'workingPaper' => $workingPaper,
        ]);
    }

    /**
     * Approve working paper
     */
    public function approve(WorkingPaper $workingPaper)
    {
        $this->authorize('review', $workingPaper);

        $workingPaper->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_comment' => null,
        ]);

        return redirect()->route('admin.dashboard')->with('success', "Working paper for {$workingPaper->user->name} ({$workingPaper->financial_year}) has been approved.");
    }

    /**
     * Reject working paper and send back to client
     */
    public function reject(Request $request, WorkingPaper $workingPaper)
    {
        $this->authorize('review', $workingPaper);

        $validated = $request->validate([
            'admin_comment' => 'required|string|min:10|max:1000',
        ]);

        $workingPaper->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_comment' => $validated['admin_comment'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', "Working paper for {$workingPaper->user->name} ({$workingPaper->financial_year}) has been rejected and returned to client.");
    }

}
