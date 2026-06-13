<?php
$periods = \App\Models\Jadwal::select('periode')->whereNotNull('periode')->distinct()->pluck('periode');
foreach($periods as $p) {
    \App\Models\Periode::firstOrCreate(['name' => $p], ['is_active' => false]);
}
echo "Periods synced: " . $periods->count() . "\n";
