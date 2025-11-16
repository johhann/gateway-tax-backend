<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgressResource;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Handle the incoming request and return user's progress.
     */
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        $profile = Profile::with(['identification', 'business', 'legal', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (! $profile) {
            return new ProgressResource([
                'percent' => 0,
                'last_saved_step' => null,
            ]);
        }

        $steps = [
            'stepOne' => true,
            'stepTwo' => true,
            'stepThree' => (bool) $profile->self_employment_income,
            'stepFive' => true,
            'stepSix' => true,
        ];

        $completed = [];
        $completed['stepOne'] = true;
        $completed['stepTwo'] = (bool) $profile->identification;
        $completed['stepThree'] = $steps['stepThree'] ? (bool) $profile->business : null;
        $completed['stepFive'] = (bool) $profile->legal;
        $completed['stepSix'] = (bool) $profile->payment;

        $applicableSteps = collect($steps)->filter()->keys();
        $total = $applicableSteps->count();

        $done = 0;
        foreach ($applicableSteps as $s) {
            if ($completed[$s]) {
                $done++;
            }
        }

        $percent = $total > 0 ? (int) floor(($done / $total) * 100) : 0;

        $last = null;
        foreach ($steps as $name => $applicable) {
            if (! $applicable) {
                continue;
            }
            if ($completed[$name]) {
                $last = $name;
            }
        }

        return new ProgressResource(['percent' => $percent, 'last_saved_step' => $last]);
    }
}
