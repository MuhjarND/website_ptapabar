<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ServiceRequest;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $requestType = $request->request_type;
        $status = $request->status;

        $serviceRequests = ServiceRequest::query()
            ->requestType(is_string($requestType) && isset(ServiceRequest::typeOptions()[$requestType]) ? $requestType : null)
            ->requestStatus(is_string($status) && isset(ServiceRequest::statusOptions()[$status]) ? $status : null)
            ->latest()
            ->paginate(20)->withQueryString();

        $requestTypes = ServiceRequest::typeOptions();
        $statuses = ServiceRequest::statusOptions();

        return view('admin.service-requests.index', compact('serviceRequests', 'requestTypes', 'statuses'));
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $statuses = ServiceRequest::statusOptions();

        return view('admin.service-requests.show', compact('serviceRequest', 'statuses'));
    }

    public function download(ServiceRequest $serviceRequest)
    {
        $path = $serviceRequest->attachment;
        abort_unless($path, 404);
        $disk = Storage::disk(Str::startsWith($path, 'private-service-requests/') ? 'local' : 'public');
        abort_unless($disk->exists($path), 404);

        return $disk->download($path, basename($path), ['Cache-Control' => 'private, no-store']);
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ServiceRequest::statusOptions()))],
            'admin_notes' => 'nullable|string',
        ]);

        $data['handled_by'] = auth()->id();
        $serviceRequest->update($data);

        ActivityLogger::record('service-request.updated', $serviceRequest, 'Memperbarui status layanan publik.', [
            'status' => $serviceRequest->status,
            'type' => $serviceRequest->request_type,
        ]);

        return redirect()->route('admin.service-requests.show', $serviceRequest)->with('success', 'Status layanan publik berhasil diperbarui.');
    }
}
