<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;

class BadgeController extends Controller
{
    public function index()
    {
        $user       = auth()->user()->load('badges');
        $allBadges  = Badge::orderBy('type')->orderBy('threshold')->get();
        $earnedIds  = $user->badges->pluck('id')->toArray();

        $grouped = $allBadges->groupBy('type')->map(function ($badges) use ($earnedIds) {
            return $badges->map(fn ($b) => array_merge($b->toArray(), [
                'earned'     => in_array($b->id, $earnedIds),
                'earned_at'  => in_array($b->id, $earnedIds)
                    ? auth()->user()->badges->find($b->id)?->pivot->earned_at
                    : null,
            ]));
        });

        $totalEarned = count($earnedIds);
        $totalBadges = $allBadges->count();

        return view('student.badges', compact('grouped', 'totalEarned', 'totalBadges'));
    }
}
