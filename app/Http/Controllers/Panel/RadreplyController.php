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

    /**
     * Import radreply entries from uploaded CSV file.
     * Expects CSV with headers: username,attribute,op,value
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = null;
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (!$header) {
                $header = array_map('strtolower', $row);
                continue;
            }

            $data = array_combine($header, $row);
            if (empty($data['username']) || empty($data['attribute'])) {
                continue;
            }

            RadReply::create([
                'username' => $data['username'],
                'attribute' => $data['attribute'],
                'op' => $data['op'] ?? '=',
                'value' => $data['value'] ?? null,
            ]);

            $imported++;
        }

        fclose($handle);

        return response()->json(['imported' => $imported]);
    }

    /**
     * Export radreply entries as CSV download.
     */
    public function export()
    {
        $rows = RadReply::all(['username','attribute','op','value'])->toArray();

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['username','attribute','op','value']);
            foreach ($rows as $row) {
                fputcsv($out, [$row['username'],$row['attribute'],$row['op'],$row['value']]);
            }
            fclose($out);
        };

        return response()->stream($callback, Response::HTTP_OK, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="radreply_export.csv"',
        ]);
    }
}
