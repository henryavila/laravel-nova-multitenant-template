<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait DefaultModelFunctions
{
	use LogsActivity;

	public function getActivitylogOptions(): LogOptions
	{
		return LogOptions::defaults()->logAll();
	}

}
