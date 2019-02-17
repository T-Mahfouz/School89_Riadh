<?php 

namespace App;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ValidPhone implements Rule
{

	public function passes($attribute, $value)
	{
		$user = DB::table('users')->where('phone', $value)->get();
		return $user;
		if($user)
		{
			return false;
		}

		return true;
	}

	public function message()
	{
		return 'phone number already taken';
	}

}

?>