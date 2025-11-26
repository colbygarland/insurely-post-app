<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerCode extends Model
{
    use SoftDeletes;

    // The unique doc that has a different structure than the rest of the docs.
    private static $partnershipDocName = 'Partnership Doc';

    private static $fieldNameMapping = [
        'FirstName' => 'first_name',
        'LastName' => 'last_name',
        'Email' => 'email',
        'Code' => 'code',
        'Fund Serve Code' => 'fund_serve_code',
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
        // Every sheet is set up the same, EXCEPT for the Partnership Doc
        // The first row is the headers
        $headers = [];
        if ($fileName != self::$partnershipDocName) {
            $headers = array_shift($data);
        }

        foreach ($data as $row) {
            $partnerCodeDataToBeCreatedOrUpdated = [];

            for ($i = 0; $i < count($row); $i++) {
                // Using the headers, get the PartnerCode key to set
                $key = self::$fieldNameMapping[$headers[$i]];
                $value = $row[$i];

                $partnerCodeDataToBeCreatedOrUpdated[$key] = $value;
            }

            $partnerCode = self::createOrUpdate($partnerCodeDataToBeCreatedOrUpdated);
            $partnerCode->save();
        }
    }

    /**
     * Searches via $data['email'] or $data['code']
     */
    public static function createOrUpdate(array $data)
    {
        $partnerCode = self::where('email', $data['email'] ?? null)
            ->orWhere('code', $data['code'] ?? null)
            ->first();

        if ($partnerCode) {
            $partnerCode->update($data);
        } else {
            $partnerCode = self::create($data);
        }

        return $partnerCode;
    }
}
