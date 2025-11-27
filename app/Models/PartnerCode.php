<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerCode extends Model
{
    use SoftDeletes;

    // The unique doc that has a different structure than the rest of the docs.
    private static $partnershipDocName = 'Partnership Doc';

    // The unique doc that has a different structure than the rest of the docs.
    private static $prolegicSolutionsDocName = 'PROLEGIS SOLUTIONS';

    private static $fieldNameMapping = [
        'FirstName' => 'first_name',
        'LastName' => 'last_name',
        'Email' => 'email',
        'Code' => 'code',
        'Fund Serve Code' => 'fund_serve_code',
        'Type' => 'type',
        'Partner/Description' => 'company',
        'Notes' => 'description',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
        'email',
        'description',
        'code',
        'fund_serve_code',
        'type',
    ];

    /**
     * Processes the data from the Excel sheet and formats it into a PartnerCode.
     * $data contains all the data from the sheet.
     * The first array is the headers (aside from the Partnership doc sheet)
     */
    public static function process(array $data, string $fileName)
    {
        // Partnership Doc is a special case
        if ($fileName === self::$partnershipDocName || $fileName === self::$prolegicSolutionsDocName) {
            self::processPartnershipDoc($data);

            return;
        }

        // Every sheet is set up the same, EXCEPT for the Partnership Doc
        // The first row is the headers
        $headers = [];
        $headers = array_shift($data);

        foreach ($data as $row) {
            $partnerCodeDataToBeCreatedOrUpdated = self::getSanitizedData($row, $headers);
            $partnerCode = self::createOrUpdate($partnerCodeDataToBeCreatedOrUpdated);
            $partnerCode->save();
        }
    }

    /**
     * GENERAL NOTES
     * - The first column of every array will be empty (the A column in the sheet)
     */
    private static function processPartnershipDoc(array $data)
    {
        // Skip the first 2 rows
        array_shift($data);
        array_shift($data);

        // The next row is our first set of headers
        // It repeats a few times as we go
        // However, it remains the same structure thankfully
        $headers = array_shift($data);

        foreach ($data as $row) {
            // Skip empty rows
            if (empty($row[1])) {
                continue;
            }

            // Handle the next set of headers
            if ($row[1] === 'Code') {
                continue;
            }

            // Handle the category breaks (ie. STRINGAM, FINANCIAL ADVISOR, etc.)
            if (! empty($row[1]) && empty($row[2])) {
                continue;
            }

            $partnerCodeDataToBeCreatedOrUpdated = self::getSanitizedData($row, $headers);
            $partnerCode = self::createOrUpdate($partnerCodeDataToBeCreatedOrUpdated);
            $partnerCode->save();
        }
    }

    private static function getSanitizedData(array $row, array $headers)
    {
        $data = [];

        for ($i = 0; $i < count($row); $i++) {
            // Using the headers, get the PartnerCode key to set
            $key = self::$fieldNameMapping[$headers[$i]] ?? null;

            // Handle the empty columns, and the columns we don't care about right now (ie Tenant, Condo, etc.)
            if (! $key) {
                continue;
            }
            $value = $row[$i];

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Searches via $data['email'] or $data['code'] or $data['fund_serve_code']
     */
    public static function createOrUpdate(array $data)
    {
        $query = self::query();

        if (! empty($data['email'])) {
            $query->orWhere('email', $data['email']);
        }

        if (! empty($data['code'])) {
            $query->orWhere('code', $data['code']);
        }

        if (! empty($data['fund_serve_code'])) {
            $query->orWhere('fund_serve_code', $data['fund_serve_code']);
        }

        $partnerCode = $query->first();

        if ($partnerCode) {
            $partnerCode->update($data);
        } else {
            $partnerCode = self::create($data);
        }

        return $partnerCode;
    }
}
