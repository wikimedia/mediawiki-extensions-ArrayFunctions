<?php

/**
 * ArrayFunctions MediaWiki extension
 * Copyright (C) 2022  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

$magicWords = [];

/** English (English) */
$magicWords['en'] = [
	// Parser functions
	'af_bool' => [ 0, 'af_bool' ],
	'af_count' => [ 0, 'af_count' ],
	'af_difference' => [ 0, 'af_difference' ],
	'af_exists' => [ 0, 'af_exists' ],
	'af_flatten' => [ 0, 'af_flatten' ],
	'af_float' => [ 0, 'af_float' ],
	'af_foreach' => [ 0, 'af_foreach' ],
	'af_group' => [ 0, 'af_group' ],
	'af_int' => [ 0, 'af_int' ],
	'af_intersect' => [ 0, 'af_intersect' ],
	'af_isarray' => [ 0, 'af_isarray' ],
	'af_join' => [ 0, 'af_join' ],
	'af_keysort' => [ 0, 'af_keysort' ],
	'af_ksort' => [ 0, 'af_ksort' ],
	'af_list' => [ 0, 'af_list' ],
	'af_map' => [ 0, 'af_map' ],
	'af_merge' => [ 0, 'af_merge' ],
	'af_object' => [ 0, 'af_object' ],
	'af_pipeline' => [ 0, 'af_pipeline' ],
	'af_get' => [ 0, 'af_get' ],
	'af_if' => [ 0, 'af_if' ],
	'af_print' => [ 0, 'af_print' ],
	'af_push' => [ 0, 'af_push' ],
	'af_put' => [ 0, 'af_put' ],
	'af_range' => [ 0, 'af_range' ],
	'af_reduce' => [ 0, 'af_reduce' ],
	'af_reverse' => [ 0, 'af_reverse' ],
	'af_search' => [ 0, 'af_search' ],
	'af_set' => [ 0, 'af_set' ],
	'af_show' => [ 0, 'af_show' ],
	'af_slice' => [ 0, 'af_slice' ],
	'af_sort' => [ 0, 'af_sort' ],
	'af_split' => [ 0, 'af_split' ],
	'af_stringmap' => [ 0, 'af_stringmap' ],
	'af_template' => [ 0, 'af_template' ],
	'af_trim' => [ 0, 'af_trim' ],
	'af_unique' => [ 0, 'af_unique' ],
	'af_unset' => [ 0, 'af_unset' ],
	'af_wildcard' => [ 0, 'af_wildcard' ],
	'af_zip' => [ 0, 'af_zip' ],
	// Magic words
	MAG_AF_EMPTY => [ 0, 'af_empty' ]
];
