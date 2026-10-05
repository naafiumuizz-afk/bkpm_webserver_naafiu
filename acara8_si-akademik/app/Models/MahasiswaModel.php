<?php

namespace App\Models;

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Mahasiswa.php';

class MahasiswaModel extends Mahasiswa
{
	/** @return list<static> */
	public static function all(string $search = '', int $limit = 10, int $offset = 0): array
	{
		return parent::all($search, $limit, $offset);
	}
}