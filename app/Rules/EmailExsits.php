<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\DB;

class EmailExsits implements InvokableRule
{
	/**
	 * Run the validation rule.
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
	 *
	 * @return void
	 */
	public function __invoke($attribute, $value, $fail)
	{
		$secondaryEmail = DB::table('users_emails')->where('email', $value)->first();
		$email = User::where('email', $value)->first();

		if ($email !== null)
		{
			$email = true;
		}
		else
		{
			$email = false;
		}

		if ($secondaryEmail !== null)
		{
			$secondaryEmail = true;
		}
		else
		{
			$secondaryEmail = false;
		}

		if ($secondaryEmail || $email)
		{
		}
		else
		{
			$fail('This email is not registered');
		}
	}
}
