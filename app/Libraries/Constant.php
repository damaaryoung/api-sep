<?php

/**
 * Library untuk mengelola konstanta.
 *
 * @package             App
 * @subpackage          Libraries
 * @author              Suluh Damar Grahita
 * @copyright           PT. Sahabat Energi Persada
 */

namespace App\Libraries;

class Constant
{
    /**
	 * General
	 */
	const REQUEST_DATA = 'request_data';
	
    const RC_SUCCESS = "00";
	const DESC_SUCCESS = "sukses";

    const RC_ERROR_VALIDATION = "VE";
	const DESC_ERROR_VALIDATION = "Validation Error";

	const RC_PARAM_NOT_VALID = "PNV";
	const DESC_PARAM_NOT_VALID = "Parameter tidak valid";

	const RC_REQUEST_NOT_VALID = "RNV";
	const DESC_REQUEST_NOT_VALID = "Format request tidak valid";

	const RC_DATA_NOT_FOUND = "DNF";
	const DESC_DATA_NOT_FOUND = "Data tidak ditemukan";

	const RC_DATA_PENDING_ALREADY = "DPA";
	const DESC_DATA_PENDING_ALREADY = "Data sudah ada pada pending outlet edc. Mohon gunakan data yang lain";

	const RC_DB_ERROR = "DE";
	const DESC_DB_ERROR = "Terjadi kesalahan pada saat akses database";
}