<?php

namespace App\Services;

use App\Models\CampusWeeklyHoliday;
use App\Models\HumanResourceLog;
use Illuminate\Support\Collection;

class CampusWeeklyHolidayService
{
    public function index(): object
    {
        return CampusWeeklyHoliday::with('campus')
            ->tenant()
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();
    }

    public function getCampusWeeklyHoliday(int $campusId): ?CampusWeeklyHoliday
    {
        return CampusWeeklyHoliday::where('campus_id', $campusId)
            ->where('tenant_id', tenant('id'))
            ->where('is_active', true)
            ->first();
    }

    public function getWeekendDaysForCampus(int $campusId): array
    {
        $holiday = $this->getCampusWeeklyHoliday($campusId);

        if (! $holiday) {
            return [0, 6]; // Default: Sunday (0) and Saturday (6)
        }

        return match ($holiday->weekend_day) {
            'saturday' => [6],
            'sunday' => [0],
            'both' => [0, 6],
            'none' => [],
            default => [0, 6],
        };
    }

    public function getWeekendDaysForTenant(): array
    {
        $defaultCampus = \App\Models\Campus::where('tenant_id', tenant('id'))->first();
        if (! $defaultCampus) {
            return [0, 6];
        }

        return $this->getWeekendDaysForCampus($defaultCampus->id);
    }

    public function submit($request): void
    {
        $validated = $request->validated();

        $existing = CampusWeeklyHoliday::where('campus_id', $validated['campus_id'])
            ->where('tenant_id', tenant('id'))
            ->first();

        if ($existing) {
            $existing->update([
                'weekend_day' => $validated['weekend_day'],
                'is_active' => $validated['is_active'] ?? true,
                'ModifiedBy' => auth()->id(),
                'ModifiedDate' => now(),
            ]);

            userActivityLogs('Campus Weekly Holiday Updated for campus ID: '.$validated['campus_id'], HumanResourceLog::class);
        } else {
            CampusWeeklyHoliday::create([
                'campus_id' => $validated['campus_id'],
                'weekend_day' => $validated['weekend_day'],
                'is_active' => $validated['is_active'] ?? true,
                'CreatedBy' => auth()->id(),
                'CreatedDate' => now(),
                'tenant_id' => tenant('id'),
            ]);

            userActivityLogs('Campus Weekly Holiday Created for campus ID: '.$validated['campus_id'], HumanResourceLog::class);
        }
    }

    public function update($request): void
    {
        $holiday = CampusWeeklyHoliday::findOrFail($request->id);

        $holiday->update([
            'campus_id' => $request->campus_id,
            'weekend_day' => $request->weekend_day,
            'ModifiedBy' => auth()->id(),
            'ModifiedDate' => now(),
        ]);

        userActivityLogs('Campus Weekly Holiday Updated ID: '.$request->id, HumanResourceLog::class);
    }

    public function destroy($id): void
    {
        $holiday = CampusWeeklyHoliday::findOrFail($id);
        $holiday->delete();

        userActivityLogs('Campus Weekly Holiday Deleted ID: '.$id, HumanResourceLog::class);
    }

    public function getCampusList(): Collection
    {
        return \App\Models\Campus::where('tenant_id', tenant('id'))
            ->where('IsActive', true)
            ->select('id', 'SchoolName')
            ->orderBy('SchoolName')
            ->get();
    }
}
