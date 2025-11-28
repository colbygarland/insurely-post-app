<?php

namespace App\Http\Controllers;

use App\Models\PartnerCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PartnerCodeController extends Controller
{
    private function authenticate(?string $key)
    {
        if ($key != env('MICROSOFT_REQUEST_KEY')) {
            throw new Exception('Unauthorized');
        }
    }

    public function index()
    {
        return view('partners.index');
        // return Inertia::render('Partners/index');
    }

    public function list(Request $request)
    {
        $this->authenticate($request->get('key'));

        $partnerCodes = PartnerCode::all();

        return response()->json([
            'meta' => [
                'count' => count($partnerCodes),
            ],
            'data' => $partnerCodes,
        ]);
    }

    public function find(Request $request)
    {
        $this->authenticate($request->get('key'));

        $searchCriteria = $request->get('searchCriteria');

        $partnerCodes = PartnerCode::query()
            ->where(function ($q) use ($searchCriteria) {
                $q->where('code', 'like', "%$searchCriteria%")
                    ->orWhere('first_name', 'like', "%$searchCriteria%")
                    ->orWhere('last_name', 'like', "%$searchCriteria%")
                    ->orWhere('email', 'like', "%$searchCriteria%")
                    ->orWhere('company', 'like', "%$searchCriteria%")
                    ->orWhere('fund_serve_code', 'like', "%$searchCriteria%")
                    ->orWhere('type', 'like', "%$searchCriteria%");
            })
            ->orderByRaw('COALESCE(first_name, company, code)')
            ->get();

        return response()->json([
            'meta' => [
                'count' => count($partnerCodes),
            ],
            'data' => $partnerCodes,
        ]);
    }

    public function create(Request $request)
    {
        $this->authenticate($request->get('key'));

        $partnerCode = new PartnerCode($request->all());
        $partnerCode->save();

        return response($partnerCode, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $this->authenticate($request->get('key'));

        $partnerCode = PartnerCode::find($id);

        if (! $partnerCode) {
            return response('Partner code not found', Response::HTTP_NOT_FOUND);
        }

        $partnerCode->first_name = $request->get('first_name');
        $partnerCode->last_name = $request->get('last_name');
        $partnerCode->company = $request->get('company');
        $partnerCode->email = $request->get('email');
        $partnerCode->description = $request->get('description');
        $partnerCode->code = $request->get('code');
        $partnerCode->fund_serve_code = $request->get('fund_serve_code');
        $partnerCode->type = $request->get('type');

        $partnerCode->save();

        return response()->json($partnerCode);
    }

    public function delete(Request $request, $id)
    {
        $this->authenticate($request->get('key'));

        if ($request->has('delete_all')) {
            PartnerCode::truncate();

            return response('All records deleted');
        }

        $partnerCode = PartnerCode::find($id);

        if (! $partnerCode) {
            return response('Partner code not found', Response::HTTP_NOT_FOUND);
        }

        $partnerCode->delete();

        return response('Partner code deleted', Response::HTTP_OK);
    }

    /**
     * Does the processing to add the PartnerCodes into the DB.
     */
    public function process(Request $request)
    {
        $this->authenticate($request->get('key'));

        $microsoftController = new MicrosoftController($request);

        // For now, hardcode the names
        // TODO: add a better way to do this
        $fileNames = [];
        if ($request->has('fileName')) {
            $fileNames = [$request->get('fileName')];
        } else {
            $fileNames = [
                'BetterLife Global Inc. - Student Sign up for GWF',
                'EAU CLAIRE PARTNERS Alpha List',
                'EXPERIOR FINANCIAL Spreadsheet Template Alpha List',
                'GREATWAY FINANCIAL Alpha List',
                'Partnership Doc',
                'PROLEGIS SOLUTIONS',
            ];
        }

        foreach ($fileNames as $fileName) {
            $newRequest = new Request(['fileName' => $fileName]);
            $response = $microsoftController->getDataFromWorksheet($newRequest);

            $data = json_decode($response->content(), true);
            PartnerCode::process($data, $fileName);
        }

        return response()->json('Data successfully processed.');
    }
}
