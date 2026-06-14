<?php

declare(strict_types=1);

namespace App\Localizing\RepositoryInterface;

/**
 * Marker contract for the LocaleTerminologyEntryEntity persistence boundary.
 *
 * Keep query semantics in the concrete repository/service layer; the contract
 * exists so Localizing can bind repositories explicitly without making SQL or
 * migrations the source of truth.
 */
interface LocaleTerminologyEntryEntityRepositoryInterface
{
}
