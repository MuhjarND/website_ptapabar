<h2>Layanan Publik Baru</h2>
<p><strong>Jenis:</strong> {{ $serviceRequest->request_type_label }}</p>
<p><strong>Nama:</strong> {{ $serviceRequest->name }}</p>
<p><strong>Email:</strong> {{ $serviceRequest->email }}</p>
@if($serviceRequest->phone)
<p><strong>Telepon:</strong> {{ $serviceRequest->phone }}</p>
@endif
<p><strong>Subjek:</strong> {{ $serviceRequest->subject }}</p>
<p><strong>Pesan:</strong><br>{{ $serviceRequest->message }}</p>
@if($serviceRequest->attachment)
<p><strong>Lampiran:</strong> {{ asset('storage/' . $serviceRequest->attachment) }}</p>
@endif
