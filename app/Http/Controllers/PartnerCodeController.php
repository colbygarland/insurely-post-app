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
        return Inertia::render('Partners/index');
    }

    public function list(Request $request)
    {
        $this->authenticate($request->get('key'));

        $partnerCodes = PartnerCode::all();

        return response()->json($partnerCodes);
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

        $partnerCode = PartnerCode::find($id);

        if (! $partnerCode) {
            return response('Partner code not found', Response::HTTP_NOT_FOUND);
        }

        $partnerCode->delete();

        return response('Partner code deleted', Response::HTTP_OK);
    }

    public function process() {}
}
