<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;

class CampaignController extends Controller
{
    public function store(Request $request)
    {
        $campaign = Campaign::create($request->all());
        return response()->json($campaign, 201);
    }

    public function show($id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json(['error' => 'Kampanya bulunamadı'], 404);
        }

        return response()->json($campaign, 200);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update($request->all());
        return response()->json($campaign, 200);
    }

    public function destroy($id)
    {
        Campaign::destroy($id);
        return response()->json(null, 204);
    }
}
