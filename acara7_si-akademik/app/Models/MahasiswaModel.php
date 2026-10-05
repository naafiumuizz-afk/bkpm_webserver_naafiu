<?php

namespace App\Models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Mahasiswa.php';

class MahasiswaModel extends Mahasiswa
{
	/** @return list<static> */
	public static function all(): array
	{
		return parent::all();
	}
}