<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RadiusService;
use App\Models\RadReply;
use Illuminate\Http\Response;

class RadreplyController extends Controller
{
    protected RadiusService $radiusService;

    public function __construct(RadiusService $radiusService)
    {
        $this->radiusService = $radiusService;
    }

    public function index()
    {
        $data = RadReply::all();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'username' => 'required|string',
            'attribute' => 'required|string',
            'value' => 'required',
            'op' => 'sometimes|string',
        ]);

        $reply = RadReply::create([
            'username' => $payload['username'],
            'attribute' => $payload['attribute'],
            'op' => $payload['op'] ?? '=',
            'value' => $payload['value'],
        ]);

        return response()->json(['data' => $reply], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $reply = RadReply::find($id);
        if (!$reply) {
            return response()->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $payload = $request->only(['attribute', 'value', 'op']);
        $reply->update(array_filter($payload, fn($v) => $v !== null));

        return response()->json(['data' => $reply]);
    }

    public function destroy($id)
    {
        $reply = RadReply::find($id);
        if (!$reply) {
            return response()->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $reply->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
