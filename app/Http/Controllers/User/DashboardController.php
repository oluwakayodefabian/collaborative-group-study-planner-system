<?php

namespace App\Http\Controllers\User;

use App\Models\FileShare;
use App\Models\StudyFile;
use App\Models\StudyGroup;
use App\Models\StudySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        $user = request()->user();
        $query = StudyFile::query();

        // Get all user-uploaded files (or all files for admin)
        $files = StudyFile::latest()->take(5)->get();


        //* Weekly uploads (past 7 days)
        $uploadsWeekly = DB::table('study_files')
            ->select(DB::raw("DATE(created_at) as label"), DB::raw("COUNT(*) as count"))
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy(DB::raw("DATE(created_at)"))
            ->get();


        //* Monthly uploads (last 6 months)
        $uploadsMonthly = DB::table('study_files')
            ->select(DB::raw("DATE_FORMAT(created_at, '%b %Y') as label"), DB::raw("COUNT(*) as count"))
            ->where('created_at', '>=', now()->subMonths(5))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
            ->orderBy(DB::raw("MIN(created_at)"))
            ->get();

        //* Recent shared files
        $recentShared = FileShare::where('shared_to', $user->id)
            ->with('file')
            ->latest()
            ->take(5)
            ->get();

        //* Total $availableGroups
        $availableGroups = StudyGroup::count();

        //* Total $joinedGroups
        $joinedGroups = StudyGroup::whereHas('members', function ($query) {
            $query->where('user_id', request()->user()->id);
        })->count();

        $sessions = StudySession::with('group')->where('end_time', '>', \Carbon\Carbon::now())->get();

        $formattedSessions = $sessions->map(function ($session) {
            return [
                'title' => $session->session_title,
                'start' => $session->start_time->toIso8601String(),
                'end'   => $session->end_time->toIso8601String(),
                // 'url'   => route('sessions.show', [$session->id]),
                'url'   => route('user.study-groups.sessions.show', [$session->group, $session]),
            ];
        });

        return view('user.dashboard', compact('title', 'files', 'uploadsWeekly', 'uploadsMonthly', 'recentShared', 'availableGroups', 'joinedGroups', 'sessions', 'formattedSessions'));
    }
}
